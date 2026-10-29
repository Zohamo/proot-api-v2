<?php

/**
 *--------------------------------------------------------------------------
 * Les fichiers JavaScript et CSS suivants seront ajoutés globalement
 * sur toutes les pages de l'application
 *--------------------------------------------------------------------------
 */

$vendorUrl = url('public/vendor');

return [
    /**
     * A insérer dans le <head> du document HTML
     */
    "head" => [
        // JQuery
        ["type" => "js",  "url" => "$vendorUrl/jquery/jquery-4.0.0.min.js"],
        // Font Awesome
        ["type" => "css", "url" => "$vendorUrl/fontawesome/css/all.min.css"],
        // Bootstrap
        ["type" => "js",  "url" => "$vendorUrl/bootstrap/js/bootstrap.bundle.min.js"],
        // Moment
        ["type" => "js",  "url" => "$vendorUrl/moment/moment.min.js"],
        ["type" => "js",  "url" => "$vendorUrl/moment/locale/fr.js"],
        // Custom
        ["type" => "css", "url" => url('css') . "/main.css"],
    ],

    /**
     * A insérer avant la fermeture de la balise </body> du document HTML
     */
    "end" => [
        // Custom
        ["type" => "js",  "url" => url('js') . "/main.js"],
    ]
];
