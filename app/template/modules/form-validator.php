<?php

return [
    // A insérer avant la fermeture de la balise </body> du document HTML
    "end" => [
        ["type" => "js", "url" => url('js') . "/form-validator.js"],
    ]
];
