<?php

namespace Core;

/**
 * Gestion des erreurs et exceptions
 */
class Error
{
    /**
     * Gestionnaire d'erreurs.
     * Convertit toutes les erreurs en Exceptions en renvoyant une ErrorException.
     *
     * @param  integer $level   Niveau d'erreur.
     * @param  string  $message Message d'erreur.
     * @param  string  $file    Nom du fichier où l'erreur s'est produite.
     * @param  integer $line    Numéro de ligne dans le fichier.
     * @return void
     * @throws \ErrorException
     */
    public static function errorHandler($level, $message, $file, $line)
    {
        if (error_reporting() !== 0) {  // pour que l'opérateur @ fonctionne
            throw new \ErrorException($message, 0, $level, $file, $line); // NOSONAR : il s'agit d'une erreur générique
        }
    }

    /**
     * Gestionnaire d'exceptions.
     *
     * @param  object $exception
     * @return void
     */
    public static function exceptionHandler($exception)
    {
        // On récupère le code d'erreur
        $code = $exception->getCode();

        // On vérifie que le code d'erreur soit supporté par PHP
        if (in_array($code, \AppConstants::HTTP_CODES_PHP_ACCEPT)) {
            http_response_code($code);
        }

        // En mode débogage on affiche l'erreur et la trace de pile
        if (env("APP_DEBUG") || (Auth::$user && Auth::$user->isDev())) {
            $msg = "<h1>Erreur fatale</h1>";
            $msg .= "<p>Code: '$code'</p>";
            $msg .= "<p>Exception: '{get_class($exception)}'</p>";
            $msg .= "<p>Message: '{$exception->getMessage()}'</p>";
            $msg .= "<p>Trace de pile:<pre>{$exception->getTraceAsString()}</pre></p>";
            $msg .= "<p>Détécté dans {$exception->getFile()}' ligne {$exception->getLine()}</p>";
            exit($msg);
        }

        // En mode débogage inactif,
        // Si le code est 401, 403 ou 404, on affiche une page d'erreur générique
        View::error($code, !in_array($code, [401, 403]));

        // Si le code est 401, 403 ou 404 on n'inscrit rien dans le journal d'erreurs
        if (!in_array($code, [401, 403, 404])) {
            // Sinon on crée un fichier log et une entrée en BDD
            (new \App\Models\LogModel)->writeAndInsert($exception);
        }
    }
}
