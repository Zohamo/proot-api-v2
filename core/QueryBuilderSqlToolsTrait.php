<?php

namespace Core;

/**
 * Ce trait contient des méthodes utiles au QueryBuilder pour la construction des requêtes SQL.
 */
trait QueryBuilderSqlToolsTrait
{
    /**
     * Renvoie la condition SQL et ses valeurs dans le cas où l'on possède la clé primaire
     * de la table.
     *
     * @param  mixed[] $data
     * @param  mixed[] $values
     * @return mixed[] ['condition' => string, 'values' => mixed[]]
     */
    private function getUpdateConditionsIfPk($data, $values)
    {
        $condition = '';
        if (is_array($this->pk)) {
            $conditions = [];
            foreach ($this->pk as $ck) {
                $conditions[] = "$ck = :$ck";
                $values[$ck] = $data[$ck];
            }
            $condition = implode(', ', $conditions);
        } else {
            $condition = "{$this->pk} = :{$this->pk}";
            $values[$this->pk] = $data[$this->pk];
        }
        return ['condition' => $condition, 'values' => $values];
    }

    /**
     * Prépare les champs et les valeurs à insérer dans la base de données :
     * - Vérifie que tous les champs requis soient présents
     * - Prépare les booléens pour MySQL (les transforme en entiers)
     * - Supprime les champs inexistants dans la table ou non modifiables en BDD
     *
     * @param  mixed[] $data
     * @param  boolean $checkRequired S'il faut renvoyer une erreur si des champs requis sont manquants.
     * @param  boolean $keepPk S'il faut préserver la clé primaire.
     * @return mixed[]
     * @throws \InvalidArgumentException
     */
    private function prepareSaveFields($data, $checkRequired = true, $keepPk = false)
    {
        if ($checkRequired) {
            $data = $this->checkRequiredFields($data);
        }
        $data = $this->prepareBooleansForMySql($data);
        $data = $this->purgeFields($data, $keepPk);

        return $data;
    }

    /**
     * Vérifie que les champs requis soient présents.
     *
     * @param  mixed[] $data
     * @return mixed[]
     */
    private function checkRequiredFields($data)
    {
        if (!method_exists($this, "attributes")) {
            return $data;
        }
        $requiredFields = $this->attributes('required');
        foreach ($requiredFields as $requiredField) {
            if (!isset($data[$requiredField])) {
                throw new \InvalidArgumentException("Le champ '$requiredField' est nécessaire pour une insertion en BDD.");
            }
        }

        return $data;
    }

    /**
     * Vérifie que les champs requis soient présents.
     *
     * @param  mixed[] $data
     * @return mixed[]
     */
    private function prepareBooleansForMySql($data)
    {
        if (!method_exists($this, "attributes")) {
            return $data;
        }
        $boolFields = $this->attributes(['type' => 'boolean']);
        if (!empty($boolFields)) {
            foreach ($boolFields as $boolField) {
                $data[$boolField] = empty($data[$boolField]) ? 0 : 1;
            }
        }

        return $data;
    }

    /**
     * Supprime les champs inexistants dans la table ou non modifiables en BDD.
     *
     * @param  mixed[] $data
     * @param  boolean $keepPk S'il faut préserver la clé primaire.
     * @return mixed[]
     */
    private function purgeFields($data, $keepPk)
    {
        if (!method_exists($this, "attributes") || !method_exists($this, "hasProperty")) {
            return $data;
        }
        $fillableFields = $this->attributes('fillable');
        foreach (array_keys($data) as $field) {
            if (
                !$this->hasProperty($field)
                || (!in_array($field, $fillableFields)
                    && (!$keepPk
                        || ($keepPk
                            && !property_exists($this, 'pk') &&
                            ((!is_array($this->pk) && $field !== $this->pk)
                                || (is_array($this->pk) && in_array($field, $this->pk))))))
            ) {
                unset($data[$field]);
            }
        }

        return $data;
    }
}
