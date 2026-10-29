<?php

namespace Core;

use Core\Entities\Route;

/**
 * Routeur
 *
 * @method static void post(string $url, string $controller, string $action = '', int|int[] $allowedRoles = []) Ajoute une route de méthode Http 'POST'.
 * @method static void get(string $url, string $controller, string $action = '', int|int[] $allowedRoles = []) Ajoute une route de méthode Http 'GET'.
 * @method static void put(string $url, string $controller, string $action = '', int|int[] $allowedRoles = []) Ajoute une route de méthode Http 'PUT'.
 * @method static void delete(string $url, string $controller, string $action = '', int|int[] $allowedRoles = []) Ajoute une route de méthode Http 'DELETE'.
 *
 * @method static void api(string $url, string $controller, int|int[] $allowedRoles = []) Ajoute les routes génériques d'un contrôleur api.
 * @method static void web(string $url, string $controller, int|int[] $allowedRoles = []) Ajoute les routes génériques d'un contrôleur web.
 */
class Router
{
    /**
     * Tableau des routes.
     * @var Route[]
     */
    public static $routes = [];

    /**
     * Ajoute une route au tableau des routes ou ajoute des routes api ou web génériques.
     *
     * @param  string $name Nom de la méthode statique appelée.
     * @param  mixed[] $arguments Arguments passés à la méthode statique.
     * @return void
     * @throws \DomainException|\BadMethodCallException
     */
    public static function __callStatic($name, $arguments)
    {
        if (in_array($name, ['post', 'get', 'put', 'delete'])) {
            self::addRoute(strtoupper($name), ...$arguments);
        } elseif (in_array($name, ['api', 'web'])) {
            self::addGenericRoutes($name, ...$arguments);
        } else {
            throw new \BadMethodCallException("'Router' ne contient pas de méthode statique '$name'.");
        }
    }

    /**
     * Ajoute une route au tableau des routes.
     *
     * @param  string $method Méthode Http de la route (post, get, put, delete).
     * @param  string $url URL de la route.
     * @param  string $controller Contrôleur à instancier.
     * @param  string $action Méthode du contrôleur à appeler.
     * @param  int|int[] $allowedRoles Identifiants des rôles qui peuvent accéder à la route.
     * @return void
     */
    public static function addRoute($method, $url, $controller, $action = '', $allowedRoles = [])
    {
        self::$routes[] = new Route($method, self::convertUrlToRegex($url), $controller, $action, $allowedRoles);
    }

    /**
     * Ajoute des routes api ou web génériques.
     *
     * @see core/assets/routes-api-generic.php
     * @see core/assets/routes-web-generic.php
     *
     * @param  string $type Type de contrôleur (web, api).
     * @param  string $url URL de la route de base (vers l'index).
     * @param  string $controller Contrôleur à instancier.
     * @param  int|int[] $allowedRoles Identifiants des rôles qui peuvent accéder à la route.
     * @return void
     */
    public static function addGenericRoutes($type, $url, $controller, $allowedRoles = [])
    {
        $routes = include "assets/routes-$type-generic.php";
        foreach ($routes as $route) {
            $action = isset($route['action']) ? $route['action'] : null;
            self::addRoute($route['method'], $url . $route['url'], $controller, $action, $allowedRoles);
        }
    }

    /**
     * Convertit une URL en expression régulière.
     *
     * @param  string $url
     * @return string
     */
    private static function convertUrlToRegex($url)
    {
        // Échappe les \
        $url = str_replace('/', '\/', $url);
        // Convertit les paramètres sans expression régulière pour n'accepter que les caractères alphanumériques, "-" et "_"
        $url = preg_replace('/\{([a-zA-Z]+)\}/', '(?P<\1>([a-zA-Z0-9_-]*))', $url);
        // Convertit les paramètres avec une expression régulière : ex: `{id:\d+}`
        $url = preg_replace('/\{([a-zA-Z]+):([^\}]+)\}/', '(?P<\1>\2)', $url);
        // Ajoute les délimiteurs de début et de fin, un "/" facultatif et une insensibilité à la casse
        return "/^$url\/?$/i";
    }

    /**
     * Récupère la route appelée, crée le contrôleur et exécute l'action correspondante.
     *
     * @return void
     * @throws \DomainException|\BadMethodCallException
     */
    public static function dispatch()
    {
        $route = self::match(self::getCurrentUrl());

        if (!$route) {
            throw new \DomainException("La page que vous recherchez n'existe pas.", 404);
        }

        $controllerName = $route->controller;
        if (!class_exists($controllerName)) {
            throw new \DomainException("Le contrôleur $controllerName est introuvable.", 404);
        }

        $controller = new $controllerName();

        $action = $route->action;
        if (!$action) {
            if (isset($route->params['action'])) {
                $action = $route->params['action'];
                $route->unsetParamAction();
            } else {
                $action = 'index';
            }
        }
        $action = \App\Functions\StringUtils::camel($action, '-');
        if (!method_exists($controller, $action)) {
            throw new \BadMethodCallException("La méthode $action() est introuvable dans le contrôleur $controllerName.", 404);
        }

        $arguments = array_values($route->params);
        $request = $_REQUEST;
        if ($route->method === "PUT") {
            $request = array_merge($request, \App\Functions\HttpUtils::getPutData());
        }
        $arguments[] = $request;
        (new History())->add($request, $route->allowedRoles);
        $controller->$action(...$arguments);
    }

    /**
     * Renvoie l'URL courante sans les paramètres de type $_GET.
     *
     * Une URL de format "localhost/?page" (une variable, aucune valeur) ne fonctionnera
     * cependant pas car le .htaccess convertira le premier "?" en "&".
     *
     * @example localhost/users?page=2&role=1 renvoie localhost/users
     *
     * @return string
     */
    private static function getCurrentUrl()
    {
        $url = $_SERVER['QUERY_STRING'];

        if (!$url) {
            return "";
        }

        $parts = explode('&', $url, 2);
        return strpos($parts[0], '=') === false
            ? $parts[0]
            : '';
    }

    /**
     * Renvoie la route correspondant à l'URL dans le tableau des routes.
     *
     * @param  string $currentUrl
     * @return Route|false
     */
    private static function match($currentUrl)
    {
        foreach (self::$routes as $route) {
            if (
                $_SERVER['REQUEST_METHOD'] === "GET"
                && preg_match($route->url, $currentUrl, $matches)
            ) {
                // Récupère les paramètres de route
                foreach ($matches as $key => $match) {
                    if (is_string($key)) {
                        $route->addParam($key, $match);
                    }
                }
                return $route;
            }
        }
        return false;
    }
}
