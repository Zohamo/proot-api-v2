<?php

$vendorUrl = url('public/vendor');

return [
    // A insérer dans le <head> du document HTML
    "head" => [
        ["type" => "css",  "url" => "$vendorUrl/select2-4.1.0-rc.0/css/select2.min.css"],
        ["type" => "css",  "url" => "$vendorUrl/select2-bootstrap-5-theme-1.3.0/select2-bootstrap-5-theme.min.css"],
        ["type" => "css",  "url" => url('css') . "/custom/select2-bootstrap.css"],
    ],
    // A insérer avant la fermeture de la balise </body> du document HTML
    "end" => [
        ["type" => "js",  "url" => "$vendorUrl/select2-4.1.0-rc.0/js/select2.full.min.js"],
        ["type" => "js",  "url" => url('js') . "/select2-init.js"],
    ]
];
