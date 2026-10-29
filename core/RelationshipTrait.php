<?php

namespace Core;

/**
 * Définit les relations entre un modèle et d'autres modèles avec des objets `Relationship`.
 */
trait RelationshipTrait
{
    /**
     * Relations entre ce modèle et d'autres modèles.
     *
     * @var \Core\Entities\Relationship[] Tableau associatif.
     */
    protected array $relationships = [];

    /**
     * Définit les relations entre ce modèle et d'autres modèles.
     *
     * Cette méthode est généralement appelée dans le constructeur.
     *
     * @return $this
     */
    protected function setRelationships()
    {
        /*  Exemple avec UtilisateurModel :
        $this->relationships = [
            'role' => new Relationship('RoleModel', 'has-one', 'fk_role_id', 'rol_id')
        ]; */
        return $this;
    }
}
