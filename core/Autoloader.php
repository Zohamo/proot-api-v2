<?php

namespace Core;

class Autoloader
{
    /**
     * Fonction permettant de charger automatiquement les classes selon leur éventuel espace de nom.
     *
     * @return void
     * @throws \LogicException
     */
    public static function register()
    {
        spl_autoload_register(function ($class) {
            $class = str_replace('\\', '/', $class);
            $file = $class === "AppConstants"
                ? "helpers/AppConstants.php"
                :  "$class.php";
            if (file_exists($file)) {
                require($file);
                return true;
            }
            throw new \LogicException("spl_autoload : Une erreur est survenue lors du chargement de '$file'");
        });
    }
}
