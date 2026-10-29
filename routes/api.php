<?php

use Core\Router;

/**
 * Définition des routes des API.
 *
 * Lors de la définition de l'URL, mettre les paramètres de route entre {},
 * exemple : "fruits/{id}/modifier".
 *
 * Par défaut, les paramètres de route n'acceptent que les caractères alphanumériques, les '-' et les '_'.
 *
 * Pour définir une expression régulière particulière à un paramètre de route, il faut l'ajouter après ':',
 * exemple : "roles/{id:\d+}/modifier", ici 'id' n'accepte que les chiffres.
 */

// Alertes
Router::get("api/alerts", "Tools\AlertsControllerApi", "getAll");

// Logs
Router::api("api/logs", "LogsControllerApi", AppConstants::ID_ROLE_DEV);

// Users
Router::api("api/users", "UsersControllerApi", AppConstants::ID_ROLE_ADMIN);

// Artists
Router::api("api/artists", "ArtistsControllerApi");

// Movies
Router::api("api/movies", "MoviesControllerApi");

// Products
Router::api("api/products", "ProductsControllerApi");

// Proojects
Router::api("api/proojects", "ProojectsControllerApi");

// Releases
Router::api("api/releases", "ReleasesControllerApi");
