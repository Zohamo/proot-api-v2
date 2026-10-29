<?php

/********************************************************************
 * INITIALISATION ET CHARGEMENT
 ********************************************************************/

// Fonctions utilitaires et de débogage
require_once 'helpers/functions.php';

// Autoloader de Composer
require_once 'vendor/autoload.php';

// Autoloader de l'application
require_once 'core/Autoloader.php';
\Core\Autoloader::register();

// Variables d'environnement (.env)
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

/********************************************************************
 * GESTION DES ERREURS ET EXCEPTIONS
 ********************************************************************/

error_reporting(E_ALL);
set_error_handler('core\Error::errorHandler');
set_exception_handler('core\Error::exceptionHandler');

/********************************************************************
 * MAINTENANCE
 ********************************************************************/

if (env("APP_IN_MAINTENANCE")) {
    if (!empty($_SERVER["QUERY_STRING"])) {
        redirect();
    }
    include path('template') . "/pages/maintenance.php";
    exit();
}

/********************************************************************
 * AUTHENTIFICATION
 ********************************************************************/

session_start();
if (!(new \Core\Auth())->login()) {
    throw new \DomainException("Vous n'êtes pas autorisé à accéder à cette application.", 401);
}

/********************************************************************
 * ROUTEUR
 ********************************************************************/

use Core\Router;

// Ajout des routes
require_once "routes/api.php";
require_once "routes/web.php";

// Recherche de la route puis aiguillage vers le contrôleur et son action
Router::dispatch();
