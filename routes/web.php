<?php

use Core\Router;

/**
 * Définition des routes web.
 *
 * Lors de la définition de l'URL, mettre les paramètres de route entre {},
 * exemple : "fruits/{id}/modifier".
 *
 * Par défaut, les paramètres de route n'acceptent que les caractères alphanumériques, les '-' et les '_'.
 *
 * Pour définir une expression régulière particulière à un paramètre de route, il faut l'ajouter après ':',
 * exemple : "roles/{id:\d+}/modifier", ici 'id' n'accepte que les chiffres.
 */

// Route par défaut
Router::get("", "HomeController", "index");

// Suppression de la session Utilisateur
Router::get("signout", "AuthController", "signout");

// Recherche
Router::get("search", "SearchController");
Router::post("search", "SearchController");

/*
 * Routes d'administration technique
 */
$idRoleDev = AppConstants::ID_ROLE_DEV;

// Journal d'erreurs
Router::get("tools/logs", "Tools\LogsController", "index", $idRoleDev);
Router::get("tools/logs/files/{filename:[a-zA-Z0-9-.]*}", "Tools\LogsController", "getFile", $idRoleDev);

// Outils
Router::get("tools/{action}", "Tools\ToolsController", null, $idRoleDev);
Router::post("tools/{action}", "Tools\ToolsController", null, $idRoleDev);

/*
 * Routes d'administration
 */
$idRoleAdmin = AppConstants::ID_ROLE_ADMIN;

// Users
Router::web("users", "UsersController", $idRoleAdmin);

/*
 * Autres routes
 */

// Artists
Router::web("artists", "ArtistsController");

// Movies
Router::web("movies", "MoviesController");

// Products
Router::web("products", "ProductsController");

// Proojects
Router::web("proojects", "ProojectsController");

// Releases
Router::web("releases", "ReleasesController");
