<?php

namespace Core;

use AppConstants;

/**
 * Gestion de l'historique de navigation.
 * Sauvegarde autant de routes que défini par $trackAmount, dans la session ($_SESSION['history'])
 * la route actuelle ayant l'index '0'.
 */
class History
{
    /**
     * Nombre de routes maximal sauvegardé dans l'historique par défaut.
     * Si la variable d'environnement "HISTORY_TRACK_AMOUNT" existe,
     * $trackAmount vaudra sa valeur.
     */
    private $trackAmount = 10;

    /**
     * Définit les propriétés.
     *
     * @return void
     */
    public function __construct()
    {
        if (env("HISTORY_TRACK_AMOUNT")) {
            $this->trackAmount = env("HISTORY_TRACK_AMOUNT");
        }
        if (empty($_SESSION['history'])) {
            $_SESSION['history'] = [];
        }
    }

    /**
     * Ajoute une route à l'historique.
     *
     * @param  mixed[] $request Tableau associatif contenant les données passées en HTTP.
     * @param  integer[] $allowedRoleIds Ids des rôles autorisés à accéder à la page.
     * @return void
     */
    public function add(array $request, $allowedRoleIds = [])
    {
        array_unshift($_SESSION['history'], [
            "date" => date(AppConstants::FORMAT_DATETIME),
            "url" => (empty($_SERVER['HTTPS']) ? 'http' : 'https') . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]",
            "method" => $_SERVER['REQUEST_METHOD'],
            "request" => $request,
            "allowedRoleIds" => $allowedRoleIds
        ]);
        if (count($_SESSION['history']) > $this->trackAmount) {
            array_splice($_SESSION['history'], $this->trackAmount);
        }
    }

    /**
     * Renvoie une route de l'historique.
     *
     * @param  integer $amount
     * @return array Tableau associatif contenant les données de la route.
     */
    public function getPrevious($amount = 1)
    {
        return !empty($_SESSION['history'][$amount]) ? $_SESSION['history'][$amount] : [];
    }

    /**
     * Redirige vers une précédente URL.
     *
     * @param  integer $amount
     * @return void
     */
    public function goBack($amount = 1)
    {
        $route = $this->getPrevious($amount);
        redirect(!empty($route) ? $route["url"] : "");
    }
}
