<?php

namespace Core;

/**
 * Ce trait contient les clauses SQL qui peuvent être ajoutées au QueryBuilder.
 */
trait QueryBuilderSqlClausesTrait
{
    /**
     * Définit les noms des champs/colonnes à renvoyer.
     *
     * @param  string $fieldNames
     * @return $this
     */
    public function fields(string ...$fieldNames)
    {
        if (isset($this->clauses['FIELDS'])) {
            $this->clauses['FIELDS'] .= ", " . implode(", ", $fieldNames);
        } else {
            $this->clauses['FIELDS'] = implode(", ", $fieldNames);
        }
        return $this;
    }

    /**
     * Ajoute une ou plusieurs tables à la requête SQL.
     * Note: La table du modèle est déjà automatiquement incluse.
     *
     * @param  string $tables Noms des tables à ajouter.
     * @return $this
     */
    public function from(string ...$tables)
    {
        if ($this->clauses['from']) {
            $this->clauses['from'] .= ", " . implode(", ", $tables);
        } else {
            $this->clauses['from'] = implode(", ", $tables);
        }
        return $this;
    }

    /**
     * Ajoute une clause de type `JOIN` à la requête SQL.
     *
     * @param  string $command
     * @param  string[] $filters
     * @return void
     */
    private function addJoinClause(string $command, string $table, array $filters): void
    {
        if (!isset($this->clauses[$command])) {
            $this->clauses[$command] = "$table ON " . implode(" AND ", $filters);
        } else {
            $this->clauses[$command] .= " $table ON " . implode(" AND ", $filters);
        }
    }

    /**
     * Ajoute une jointure `JOIN` (`INNER JOIN`) à la requête SQL.
     *
     * @param  string $table Nom de la table à joindre.
     * @param  string $filters Ex: "id = fk_id".
     * @return $this
     */
    public function join(string $table, string ...$filters)
    {
        $this->addJoinClause("INNER JOIN", $table, $filters);
        return $this;
    }

    /**
     * Ajoute une jointure `LEFT JOIN` à la requête SQL.
     *
     * @param  string $table Nom de la table à joindre.
     * @param  string $filters Ex: "id = fk_id".
     * @return $this
     */
    public function leftJoin(string $table, string ...$filters)
    {
        $this->addJoinClause("LEFT JOIN", $table, $filters);
        return $this;
    }

    /**
     * Ajoute une jointure `RIGHT JOIN` à la requête SQL.
     *
     * @param  string $table Nom de la table à joindre.
     * @param  string $filters Ex: "id = fk_id".
     * @return $this
     */
    public function rightJoin(string $table, string ...$filters)
    {
        $this->addJoinClause("RIGHT JOIN", $table, $filters);
        return $this;
    }

    /**
     * Ajoute une jointure `CROSS JOIN` à la requête SQL.
     *
     * @param  string $table Nom de la table à joindre.
     * @param  string $filters Ex: "id = fk_id".
     * @return $this
     */
    public function crossJoin(string $table, string ...$filters)
    {
        $this->addJoinClause("CROSS JOIN", $table, $filters);
        return $this;
    }

    /**
     * Ajoute une clause `WHERE` à la requête SQL.
     * Note: s'ajoute à la précédente avec `AND`.
     *
     * @param  string $filters Ex: "x = ?", "y = 12".
     * @return $this
     */
    public function where(string ...$filters)
    {
        $sql = implode(" AND ", $filters);
        $this->clauses["WHERE"] = empty($this->clauses["WHERE"])
            ? "($sql)"
            : $this->clauses["WHERE"] . " AND ($sql)";
        return $this;
    }

    /**
     * Ajoute une clause `GROUP BY` à la requête SQL.
     * Note: écrase la précédente.
     *
     * @param  string $columns
     * @return $this
     */
    public function group(string ...$columns)
    {
        $this->clauses["GROUP BY"] = implode(", ", $columns);
        return $this;
    }

    /**
     * Ajoute une clause `ORDER BY` à la requête SQL.
     * Permet d'ordonner les résultats renvoyés.
     * Note: écrase la précédente.
     *
     * @param  string $columns Ex: "a DESC", "b ASC".
     * @return $this
     */
    public function order(string ...$columns)
    {
        $this->clauses["ORDER BY"] = implode(", ", $columns);
        return $this;
    }

    /**
     * Ajoute une clause `HAVING` à la requête SQL.
     * Note: s'ajoute à la précédente avec `AND`.
     *
     * @param  string $filters
     * @return $this
     */
    public function having(string ...$filters)
    {
        $sql = implode(" AND ", $filters);
        $this->clauses["HAVING"] = empty($this->clauses["HAVING"])
            ? "($sql)"
            : $this->clauses["HAVING"] . " AND ($sql)";
        return $this;
    }

    /**
     * Ajoute une clause `LIMIT` à la requête SQL.
     * Note: écrase la précédente.
     *
     * @param  int $value Nombre maximal de lignes à renvoyer.
     * @return $this
     */
    public function limit(int $value)
    {
        $this->clauses["LIMIT"] = $value;
        return $this;
    }
}
