<?php

namespace Core;

use Core\Entities\Relationship;

/**
 * Ce trait gère les relations avec d'autres modèles.
 * Il doit être placé dans le QueryBuilder pour pouvoir ajouter les clauses SQL aux requêtes.
 */
trait ModelRelationshipsTrait
{
    use RelationshipTrait;

    /**
     * Relations ajoutées à la requête SQL.
     *
     * @var Relationship[]
     */
    protected array $with = [];

    /**
     * Espace de nom des modèles.
     *
     * @var string
     */
    protected static string $modelsDir = "\App\Models\\";

    /**
     * Renvoie le nom de la table du modèle.
     *
     * @return string
     */
    public function getTable()
    {
        return $this->table;
    }

    /**
     * Permet de récupérer la relation d'un modèle dans une requête `SELECT`.
     *
     * @param  string $relationNames Nom donné à la relation (clé).
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function with(...$relationNames)
    {
        if (!is_array($relationNames)) {
            $relationNames = [$relationNames];
        }
        foreach ($relationNames as $relationName) {
            if (!in_array($relationName, array_keys($this->relationships))) {
                throw new \InvalidArgumentException("La relation '$relationName' n'a pas été définie dans le modèle '" . (new \ReflectionClass($this))->getShortName() . "'.");
            }
            if (!isset($this->with[$relationName])) {
                $this->with[$relationName] = $this->relationships[$relationName];
                $this->setRelationClause($this->relationships[$relationName]);
            }
        }

        return $this;
    }

    /**
     * Ajoute la clause SQL de la relation.
     *
     * @param  Relationship $rel
     * @return void
     * @throws \LogicException
     */
    private function setRelationClause(Relationship $rel)
    {
        $modelName = self::$modelsDir . $rel->relationClassName;
        if (!class_exists($modelName)) {
            throw new \LogicException("La classe '$modelName' n'existe pas");
        }
        $model = new $modelName();
        $rel->setRelation($model);

        if (!in_array($rel->type, ["has-one", "has-many"])) {
            throw new \LogicException("Le type de relation '$rel->type' n'est pas reconnu.");
        }

        if ($rel->type === "has-one") {
            $sql = "{$model->getTable()} ON {$this->getTable()}.{$rel->foreignKey} = {$model->getTable()}." . ($rel->relatedKey ?: $model->pk);
            if (!isset($this->clauses['LEFT JOIN'])) {
                $this->clauses['LEFT JOIN'] = $sql;
            } else {
                $this->clauses['LEFT JOIN'] .= " $sql";
            }
        }
    }

    /**
     * Renvoie le résultat d'une requête SELECT avec ses relations.
     * - relation 'has-one' : définit les propriétés de l'objet lié,
     * - relation 'has-many' : crée une requête pour récupérer un tableau d'objets liés.
     *
     * @param  array[] $result Résultat de la requête SQL.
     * @param  string $entity Entité à instancier.
     * @return array
     */
    public function mapResultRelations(array $result, string $entity)
    {
        return array_map(
            empty($this->with)
                ? fn(array $row) => $entity::fromArray($row)
                : function (array $row) use ($entity) {
                    $entity = $entity::fromArray($row);
                    foreach ($this->with as $relationName => $rel) {
                        switch ($rel->type) {
                            case "has-one":
                                $relatedEntity = "\App\Entities\\" . $rel->relation->entity;
                                $entity->$relationName = (new $relatedEntity())::fromArray($row);
                                break;
                            case "has-many":
                                $entity->$relationName = $rel->relation
                                    ->where([$rel->foreignKey . " = ?"])
                                    ->select([$row[$rel->relatedKey]]);
                                break;
                            default:
                                throw new \LogicException("Le type de relation '$rel->type' n'est pas reconnu.");
                        }
                    }
                    return $entity;
                },
            $result
        );
    }
}
