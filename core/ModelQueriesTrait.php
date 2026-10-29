<?php

namespace Core;

/**
 * Ce trait contient les requêtes SQL communes aux modèles.
 */
trait ModelQueriesTrait
{
    /**
     * Renvoie la liste des tous les modèles enregistrés.
     *
     * @return object[]|array[]|boolean
     */
    public function all()
    {
        return $this->select();
    }

    /**
     * Renvoie une liste de modèles respectant certains critères.
     *
     * Opérateurs acceptés : '=', '!=', '<', '>', '<=', '>=', 'IN', 'NOT IN', 'LIKE', 'AND', 'OR'.
     *
     * @example ModelQueries.php Renvoyer les modèles où :
     * - la clé primaire vaut 3 :
     *      `$this->model->find(3)`
     *
     * - `email` vaut "cutest" :
     *      `$this->model->find('email', 'cutest')`
     * - `email` ne vaut pas "cutest" :
     *      `$this->model->find('email', '!=', 'cutest')`
     * - `prenom` vaut "Bernard' OU "Roger" :
     *      `$this->model->find('prenom', 'IN', ['Bernard', 'Roger'])`
     *
     * - `prenom` vaut "Bernard" ET `nom` vaut "Dupond" :
     *      `$this->model->find(['prenom', 'Bernard'], ['nom', 'Dupond'])`
     * - `prenom` vaut "Bernard" OU `nom` vaut "Dupond" :
     *      `$this->model->find(['prenom', 'Bernard'], 'OR', ['nom', 'Dupond'])`
     *
     * @param  mixed|mixed[] $params [$champ(s), ($opérateur,) $valeur(s)]
     * @return object[]|array[]|boolean
     * @throws \InvalidArgumentException
     */
    public function find(...$params)
    {
        $whereArgs = [];
        $values = [];
        if (count($params) === 1) {
            $whereArgs = $this->pk . " = ?";
            $values = [$params[0]];
        } else {
            ['whereArgs' => $whereArgs, 'values' => $values] = is_array($params[0])
                ? $this->buildWhereConditionForNestedArgs($params, $values)
                : $this->buildWhereCondition($params);
        }
        return $this->where($whereArgs)->select($values);
    }

    /**
     * Construit et renvoie un tableau avec une clause WHERE d'une requête et sa valeur.
     *
     * @param  mixed[] $params
     * @return mixed[]
     */
    private function buildWhereCondition(array $params)
    {
        $values = [];
        $whereArgs = $params[0];
        if (in_array(strtoupper(trim($params[1])), ['=', '!=', '<', '>', '<=', '>=', 'IN', 'NOT IN', 'LIKE'])) {
            $whereArgs .= " " . $params[1] . " ";
            if (is_array($params[2])) {
                $whereArgs .= '(' . implode(', ', array_fill(0, count($params[2]), '?')) . ')';
                $values = $params[2];
            } else {
                $whereArgs .= "?";
                $values = [$params[2]];
            }
        } else {
            $whereArgs .= " = ?";
            $values = [$params[1]];
        }
        return ['whereArgs' => $whereArgs, 'values' => $values];
    }

    /**
     * Construit la clause WHERE d'une requête avec différents arguments imbriqués.
     *
     * @param  mixed[] $params
     * @param  mixed $values
     * @return mixed[]
     */
    private function buildWhereConditionForNestedArgs(array $params, $values)
    {
        $whereArgs = [];
        foreach ($params as $param) {
            if (is_string($param)) {
                $separator = strtoupper(trim($param));
                if ($separator == 'OR' || $separator == 'AND') {
                    $whereArgs['separator'] = " $separator ";
                }
            } elseif (is_array($param)) {
                $condition = $this->buildWhereCondition($param);
                $whereArgs[] = $condition['whereArgs'];
                $values = array_merge($values, $condition['values']);
            } else {
                throw new \InvalidArgumentException("La syntaxe de la requête est incorrecte.");
            }
        }
        return ['whereArgs' => implode(" AND ", $whereArgs), 'values' => $values];
    }

    /**
     * Renvoie le premier modèle trouvé, respectant les critères `$params`.
     *
     * @see find() de ModelQueriesTrait.php
     *
     * @param  int|string|array $params
     * @return object|mixed[]|boolean Renvoie `false` si aucun modèle ne correspond aux critères demandés.
     */
    public function first(...$params)
    {
        $result = !empty($params)
            ? $this->limit(1)->find(...$params)
            : $this->limit(1)->select(); // renvoie le premier modèle de la table
        if (is_bool($result)) {
            return $result;
        }
        return !empty($result) ? $result[0] : false;
    }

    /**
     * Enregistre un modèle dans la BDD.
     *
     * Si le tableau contient les clés composites ou la clé primaire de la table,
     * le modèle sera modifié ('UPDATE'), sinon il sera ajouté ('INSERT') à la BDD.
     *
     * @param  array $data Tableau associatif de données.
     * @return bool|integer
     */
    public function save(array $data = [])
    {
        // On alimente le modèle avec les données
        $this->hydrate($data);

        // Si les propriétés contiennent les clés composites ou la clé primaire
        // on sait qu'il s'agit d'une modification ('UPDATE')
        $insert = true;
        if (is_array($this->pk)) {
            foreach ($this->pk as $ck) {
                if (!in_array($ck, array_keys($this->properties))) {
                    break;
                } else {
                    $insert = false;
                }
            }
        } elseif (in_array($this->pk, array_keys($this->properties))) {
            $insert = false;
        }

        return $insert ? $this->insert() : $this->update();
    }

    /**
     * Supprime un modèle de la BDD.
     *
     * @param  int|string|array $params
     *     Peut avoir 2 types de valeur :
     *     - Valeur de la clé primaire à trouver.
     *       ex: `$this->model->remove(3)`
     *     - Tableau comportant :
     *         [0] => la colonne où chercher,
     *         [1] => la valeur à trouver dans cette colonne.
     *       ex: `$this->model->remove(['email', 'cutest'])`
     * @return bool `true`=succès, `false`=échec
     */
    public function remove($params)
    {
        if (is_array($params)) {
            return $this->where($params[0] . " = ?")
                ->delete([$params[1]])[0] === 1;
        }
        return $this->where($this->pk . " = ?")
            ->delete([$params]) === 1;
    }

    /**
     * Renvoie les résultats d'une recherche.
     *
     * @param  string $query Chaîne de caractères à trouver.
     * @return object[]|array[]|boolean
     */
    public function search(string $query)
    {
        return $this->fields(...$this->attributes(['hidden' => false]))
            ->where(\App\Functions\SqlUtils::searchQueryBuilder($this->attributes('search'), $query))
            ->select();
    }
}
