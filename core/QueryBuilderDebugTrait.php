<?php

namespace Core;

/**
 * Ce trait contient des méthodes utiles au QueryBuilder pour le débogage.
 */
trait QueryBuilderDebugTrait
{
    /**
     * Modes de débogage pour afficher la requête SQL exécutée.
     * @var string 'debug'(défaut)|'dieBefore'|'dieAfter'
     */
    private $debug;

    /**
     * Timestamp en millisecondes avant de passer la requête.
     * @var float
     */
    private $startQueryTime;

    /**
     * Sauvegarde de toutes les requêtes SQL exécutées.
     * @var array[] ["sql" => (string), "values" => (array), "className" => (string)]
     */
    static $queries = [];

    /**
     * Identifiant de la requête en cours.
     * @var integer
     */
    static $queryIndex = 0;

    /**
     * Active le débogage de la requête à exécuter.
     *
     * Modes :
     * - 'debug'(défaut)
     * - 'dieBefore': arrête le script avant l'éxécution de la requête
     * - 'dieAfter': arrête le script après l'éxécution de la requête
     *
     * @param  string $mode 'debug', 'dieBefore' ou 'dieAfter'.
     * @return $this
     */
    public function debug($mode = 'debug')
    {
        $this->debug = $mode;
        return $this;
    }

    /**
     * Méthode à appeler avant l'exécution d'une requête.
     *
     * Contient l'affichage de la requête pour débogage.
     *
     * @param  string $query
     * @param  mixed[] $values
     * @param  string|null $className
     * @return void
     */
    private function beforeExecute($query, $values, $className = null) // NOSONAR : Variables utilisées dans le fichier inclus
    {
        self::$queryIndex++;
        self::$queries[self::$queryIndex] = [
            "sql" => $query,
            "values" => $values,
            "className" => $className,
            "backtrace" => debug_backtrace()
        ];

        $this->startQueryTime = microtime(true);

        if (!$this->debug) {
            return;
        }

        require path('template') . "/components/debug-query.php";

        if ($this->debug === 'dieBefore') {
            exit;
        }
    }

    /**
     * Méthode à appeler après l'exécution d'une requête.
     *
     * Contient l'affichage du résultat de l'exécution de la requête
     * et la réinitialisation du QueryBuilder.
     *
     * @param  mixed $result
     * @return void
     */
    private function afterExecute($result) // NOSONAR : Variables utilisées dans le fichier inclus
    {
        // Calcul du temps d'exécution
        $time = microtime(true) - $this->startQueryTime;
        self::$queries[self::$queryIndex]['time'] = $time;

        if ($this->debug) {
            require path('template') . "/components/debug-query-result.php";
            if ($this->debug === 'dieAfter') {
                exit;
            }
        }

        $this->resetQB();
    }
}
