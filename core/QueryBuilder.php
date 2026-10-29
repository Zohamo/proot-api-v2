<?php

namespace Core;

/**
 * Constructeur des requêtes SQL.
 * S'utilise le plus couramment à partir du modèle, mais peut également être utilisé indépendamment en l'instanciant.
 */
class QueryBuilder
{
    use ModelRelationshipsTrait,
        QueryBuilderDebugTrait,
        QueryBuilderSqlToolsTrait,
        QueryBuilderSqlClausesTrait,
        QueryBuilderSqlTransactionTrait;

    /**
     * Clauses SQL pour construire la requête.
     * @var array
     */
    protected array $clauses;

    /**
     * Type de données à renvoyer en retour de la requête SQL.
     * @var string
     */
    private string $returnType = "";

    /**
     * Nom de la table du modèle.
     * @var string ex: `t_users`
     */
    protected string $table;

    /**
     * Nom de la clé primaire de la table ou des clés composites.
     * @example: `id` ou [`fk_id`, `fk_role_id`]
     */
    protected string|array $pk = "";

    /**
     * Nom de l'entité relative au modèle.
     * @example: "Utilisateur"
     * @var string
     */
    protected string $entity = "";

    /**
     * Définit les propriétés du QueryBuilder.
     *
     * @param  string $dbKey Identifiant de la configuration de BDD à utiliser (ex: "DB_$dbKey_HOST").
     * @return void
     */
    public function __construct($dbKey = "")
    {
        if (!self::$dao || !self::$dao->inTransaction()) {
            self::$dao = new DAO($dbKey);
        }
    }

    /**
     * Renvoie un booléen au lieu du résultat de l'exécution de la requête.
     * - succès : `true`
     * - échec : `false`
     *
     * @return $this
     */
    public function toBool()
    {
        $this->returnType = 'bool';
        return $this;
    }

    /**
     * Renvoie un tableau de tableaux associatifs au lieu d'un tableau d'objets lors d'une requête de type SELECT.
     *
     * @return $this
     */
    public function toArray()
    {
        $this->returnType = 'array';
        return $this;
    }

    /**
     * Réinitialise le QueryBuilder pour pouvoir utiliser la même instance pour plusieurs requêtes.
     *
     * @return void
     */
    protected function resetQB()
    {
        $this->clauses = [];
        $this->returnType = '';
        $this->debug = false;
    }

    /**
     * Renvoie le nom de la table où faire la requête.
     * Si aucune table n'a été définie dans les clauses, on récupère la table définie
     * dans l'objet (modèle).
     *
     * @return string
     */
    public function getRequestTable()
    {
        return isset($this->clauses['FROM']) ? $this->table . ", " . $this->clauses['FROM'] : $this->table;
    }

    /**
     * Construit une requête `SELECT` et renvoie le résultat.
     *
     * @param  mixed[] $values Valeurs pour construire la requête.
     * @param  bool $distinct S'il faut exécuter une requête `SELECT DISTINCT`.
     * @return object[]|array[]|boolean Résultat de la requête.
     */
    public function select(array $values = [], bool $distinct = false)
    {
        $sql = "SELECT " . ($distinct ? "DISTINCT " : "");
        $sql .= isset($this->clauses['FIELDS']) ? $this->clauses['FIELDS'] . " " : "* ";
        $sql .= "FROM " . $this->getRequestTable() . " ";
        foreach (["INNER JOIN", "LEFT JOIN", "RIGHT JOIN", "CROSS JOIN", "WHERE", "GROUP BY", "ORDER BY", "HAVING", "LIMIT"] as $c) {
            if (isset($this->clauses[$c])) {
                $sql .= "$c {$this->clauses[$c]} ";
            }
        }

        $entity = $this->returnType || empty($this->entity)
            ? null : "\App\Entities\\" . $this->entity;

        $this->beforeExecute($sql, $values, $entity);
        $result = self::$dao->executeSelect($sql, $values);

        if (!empty($result) && $entity) {
            $result = $this->mapResultRelations($result, $entity);
        }

        // Sauvegarde du type de retour avant la réinitialisation du Query Builder
        $returnBool = $this->returnType === 'bool';
        $this->afterExecute($result);

        return $returnBool ? !empty($result) : $result;
    }

    /**
     * Construit une requête SQL `INSERT` et renvoie le résultat.
     *
     * @param  mixed[] $data Tableau associatif des données à mettre à jour.
     * @return int|bool|$this Dernier id. inséré, `false` en cas d'erreur ou `$this` si transaction.
     * @throws \InvalidArgumentException
     */
    public function insert(array $data = [])
    {
        $table = $this->getRequestTable();
        if (!$table) {
            throw new \InvalidArgumentException("Aucune table n'a été définie pour faire la requête.");
        }
        $data = $this->prepareSaveFields($data);
        if (empty($data)) {
            throw new \InvalidArgumentException("Aucune donnée à insérer lors de la requête.");
        }
        $fields = array_keys($data);
        $placeholders = array_map(function ($field) {
            return ":$field";
        }, $fields);
        $placeholders = implode(', ', $placeholders);
        $fields = implode(", ", $fields);

        $sql = "INSERT INTO $table ($fields) VALUES ($placeholders);";

        $this->beforeExecute($sql, $data);
        $result = self::$dao->executeInsert($sql, $data);
        // Sauvegarde du type de retour avant la réinitialisation du Query Builder
        $returnBool = $this->returnType === 'bool';
        $this->afterExecute($result);

        if (self::$dao->inTransaction()) {
            return $result === false ? false : $this;
        }

        return $returnBool ? $result !== false : $result;
    }

    /**
     * Construit une requête SQL `INSERT` multiple et renvoie le résultat.
     *
     * @param  mixed[][] $multiData Tableau de tableaux associatifs des données à mettre à jour.
     * @return int|bool|$this Premier des derniers ids insérés, `false` en cas d'erreur ou `$this` si transaction.
     * @throws \InvalidArgumentException
     */
    public function multiInsert(array $multiData)
    {
        $table = $this->getRequestTable();

        if (empty($multiData) || !is_array($multiData[0])) {
            throw new \InvalidArgumentException("Une insertion multiple doit comporter plusieurs tableaux de valeurs.");
        }

        $multiData = array_map(function ($data) {
            return $this->prepareSaveFields($data);
        }, $multiData);
        $fields = array_keys($multiData[0]);
        $placeholders = [];
        $values = [];
        foreach ($multiData as $i => $data) {
            $rowPlaceholders = [];
            foreach ($fields as $field) {
                if (!isset($data[$field])) {
                    throw new \InvalidArgumentException("Les données ne sont pas cohérentes pour une insertion multiple : au moins un champ est manquant.");
                }
                $placeholder = ":$field$i";
                $rowPlaceholders[] = $placeholder;
                $values["$field$i"] = $data[$field];
            }
            $placeholders[] = "(" . implode(", ", $rowPlaceholders) . ")";
        }
        $placeholders = implode(", ", $placeholders);
        $fields = implode(", ", $fields);

        $sql = "INSERT INTO $table ($fields) VALUES $placeholders";

        $this->beforeExecute($sql, $values);
        $result = self::$dao->executeInsert($sql, $values);
        // Sauvegarde du type de retour avant la réinitialisation du Query Builder
        $returnBool = $this->returnType === 'bool';
        $this->afterExecute($result);

        if (self::$dao->inTransaction()) {
            return $this;
        }

        return $returnBool ? $result !== false : $result;
    }

    /**
     * Construit une requête `UPDATE` et renvoie le résultat.
     *
     * @param  mixed[] $data Tableau associatif des données à mettre à jour.
     * @param  mixed[] $filters Données pour construire la requête (conditions, etc.).
     * @return int|bool|$this Nombre de lignes modifiées, `false` en cas d'erreur ou `$this` si transaction.
     * @throws \BadMethodCallException
     */
    public function update(array $data = [], array $filters = [])
    {
        // Récupération des clauses SQL
        $table = $this->getRequestTable();
        $join = isset($this->clauses['join']) ? $this->clauses['join'] : '';

        // Préparation des données
        $data = $this->prepareSaveFields($data, false, true);

        // Ajout des sets de données
        $sets = [];
        foreach (array_keys($data) as $field) {
            if (!property_exists($this, 'pk') || $this->pk != $field) {
                $sets[] = "$field = :$field";
            }
        }
        $sets = implode(", ", $sets);

        // Préparation des conditions
        $values = array_merge($data, $filters);
        $condition = '';
        if (isset($this->clauses['WHERE'])) {
            $condition = $this->clauses['WHERE'];
        } elseif (property_exists($this, 'pk') && method_exists($this, 'getPk')) {
            ['condition' => $condition, 'values' => $values] = $this->getUpdateConditionsIfPk($data, $values);
        } else {
            throw new \BadMethodCallException("Une requête SQL 'UPDATE' nécéssite une clause 'WHERE'.");
        }

        $sql = "UPDATE $table $join SET $sets WHERE $condition;";

        $this->beforeExecute($sql, $values);
        $result = self::$dao->executeUpdate($sql, $values);
        // Sauvegarde du type de retour avant la réinitialisation du Query Builder
        $returnBool = $this->returnType === 'bool';
        $this->afterExecute($result);

        if (self::$dao->inTransaction()) {
            return $this;
        }

        return $returnBool ? $result !== false : $result;
    }

    /**
     * Construit une requête SQL `INSERT INTO` multiple et renvoie le résultat.
     *
     * @param  mixed[][] $multiData Tableau de tableaux associatifs des données à mettre à jour.
     * @return int|bool Premier des derniers ids insérés ou `false` en cas d'erreur.
     * @throws \InvalidArgumentException
     */
    public function multiUpdate(array $multiData)
    {
        if (empty($multiData) || !is_array($multiData[0])) {
            throw new \InvalidArgumentException("Un 'INSERT' multiple doit comporter plusieurs tableaux de valeurs.");
        }
        // Récupération des clauses SQL
        $table = $this->getRequestTable();

        $multiData = array_map(function ($data) {
            return $this->prepareSaveFields($data, false, true);
        }, $multiData);


        $fields = array_keys($multiData[0]);
        $placeholders = [];
        $values = [];
        $sets = [];
        foreach ($multiData as $i => $data) {
            $rowPlaceholders = [];
            foreach ($fields as $field) {
                if (!isset($data[$field])) {
                    throw new \InvalidArgumentException("Les données ne sont pas cohérentes pour un 'INSERT' multiple : au moins un champ est manquant.");
                }
                $placeholder = ":$field$i";
                $rowPlaceholders[] = $placeholder;
                $values["$field$i"] = $data[$field];
                $sets[] = "$field = VALUES($field)";
            }
            $placeholders[] = "(" . implode(", ", $rowPlaceholders) . ")";
        }
        $placeholders = implode(", ", $placeholders);
        $fields = implode(", ", $fields);
        $sets = implode(", ", $sets);

        $sql = "INSERT INTO $table ($fields) VALUES $placeholders ON DUPLICATE KEY UPDATE $sets;";

        $this->beforeExecute($sql, $values);
        $result = self::$dao->executeInsert($sql, $values);
        // Sauvegarde du type de retour avant la réinitialisation du Query Builder
        $returnBool = $this->returnType === 'bool';
        $this->afterExecute($result);

        if (self::$dao->inTransaction()) {
            return $this;
        }

        return $returnBool ? $result !== false : $result;
    }

    /**
     * Construit une requête `DELETE` et renvoie le résultat.
     *
     * @param  mixed[] $filters Valeurs pour construire la requête.
     * @return int|bool Nombre de lignes supprimées ou `false` en cas d'erreur.
     */
    public function delete(array $filters = [])
    {
        $table = $this->getRequestTable();
        $join = isset($this->clauses['join']) ? $this->clauses['join'] : '';

        $command = ['DELETE FROM', $table];
        if ($join) {
            $command[] = $join;
        }

        if (isset($this->clauses['WHERE'])) {
            $command[] = "WHERE {$this->clauses['WHERE']}";
        }

        $sql = implode(' ', $command);

        $this->beforeExecute($sql, $filters);
        $result = self::$dao->executeDelete($sql, $filters);
        // Sauvegarde du type de retour avant la réinitialisation du Query Builder
        $returnBool = $this->returnType === 'bool';
        $this->afterExecute($result);

        if (self::$dao->inTransaction()) {
            return $this;
        }

        return $returnBool ? $result !== false : $result;
    }

    /**
     * Construit une requête d'insertion de procédure stockée et renvoie le résultat.
     *
     * @param  \App\Entities\StoredProcedure $procedure
     * @return bool
     */
    public function insertProcedure(\App\Entities\StoredProcedure $procedure)
    {
        // On supprime la procédure si elle existe
        self::$dao->executeDeleteProcedure($procedure->nom);

        return self::$dao->executeInsertProcedure($procedure->query());
    }

    /**
     * Appelle une procédure stockée et renvoie le résultat.
     *
     * @param  string $name   Nom de la procédure.
     * @param  array  $params Paramètres de la procédure.
     * @return bool
     */
    public function callProcedure($name, $params = [])
    {
        return self::$dao->executeCallProcedure($name, $params);
    }

    /**
     * Exécute une requête SQL et renvoie le résultat.
     *
     * @param  string  $query     Requête SQL à exécuter.
     * @param  array   $values    Valeurs à passer à la requête.
     * @param  string  $className Nom de la classe pour renvoyer un tableau d'objets.
     * @return mixed Résultat d'exécution de la requête.
     */
    public function query($query, $values = [], $className = null)
    {
        $command = ucfirst(strtolower(explode(' ', trim($query))[0]));
        $command = trim("execute$command");

        $this->beforeExecute($query, $values, $className);
        $result = self::$dao->$command($query, $values, $className);
        $this->afterExecute($result);

        if (self::$dao->inTransaction()) {
            return $this;
        }

        return $result;
    }

    /**
     * Exécute une requête SQL et renvoie le premier résultat.
     *
     * @param  string  $query     Requête SQL à exécuter.
     * @param  array   $values    Valeurs à passer à la requête.
     * @param  string  $className Nom de la classe pour renvoyer un objet.
     * @return mixed Premier résultat d'exécution de la requête ou résultat d'exécution de la requête.
     */
    public function queryOne($query, $values = [], $className = null)
    {
        $this->beforeExecute($query, $values, $className);
        $res = $this->query($query, $values, $className);
        $this->afterExecute($res);

        return !is_array($res) || empty($res) ? $res : $res[0];
    }

    /**
     * Renvoie le Checksum d'une ou plusieurs tables.
     *
     * @param  string|string[] $table Nom de la ou des table(s).
     * @return int|mixed[]
     * @throws \Exception
     */
    public function getChecksum($table = '')
    {
        if (!is_array($table)) {
            $table = $table ?: $this->getRequestTable();
            return self::$dao->executeChecksum("CHECKSUM TABLE $table")[0]["Checksum"];
        }
        $table = join(', ', $table);

        return self::$dao->executeChecksum("CHECKSUM TABLE $table");
    }
}
