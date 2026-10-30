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

// Artists
Router::get("artists/{slug}", "ArtistsControllerApi", "get");
Router::get("artists", "ArtistsControllerApi", "getAll");

// Movies
Router::get("movies/{slug}", "MoviesControllerApi", "get");
Router::get("movies", "MoviesControllerApi", "getAll");

// Products
Router::get("products/{slug}", "ProductsControllerApi", "get");
Router::get("products", "ProductsControllerApi", "getAll");

// Proojects
Router::get("proojects/{slug}", "ProojectsControllerApi", "get");
Router::get("proojects", "ProojectsControllerApi", "getAll");

// Releases
Router::get("releases/{ref}", "ReleasesControllerApi", "get");
Router::get("releases", "ReleasesControllerApi", "getAll");
