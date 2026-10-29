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
Router::get("movies", "MoviesControllerApi", "getAll");
Router::get("movies/{id}", "MoviesControllerApi", "get");

// Products
Router::get("products", "ProductsControllerApi", "getAll");
Router::get("products/{id}", "ProductsControllerApi", "get");

// Proojects
Router::get("proojects", "ProojectsControllerApi", "getAll");
Router::get("proojects/{id}", "ProojectsControllerApi", "get");

// Releases
Router::get("releases", "ReleasesControllerApi", "getAll");
Router::get("releases/{id}", "ReleasesControllerApi", "get");
