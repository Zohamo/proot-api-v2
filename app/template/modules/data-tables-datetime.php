<?php

$vendorUrl = url('public/vendor');

return [
    // A insérer avant la fermeture de la balise </body> du document HTML
    "end" => [
        ["type" => "js",  "url" => url('js') . "/data-tables-datetime-init.js"],
    ]
];
