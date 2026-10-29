<?php

namespace App\Models;

use Core\AbstractModel;
use Core\ModelQueriesTrait;

class ArtistHasRelationshipModel extends AbstractModel
{
    use ModelQueriesTrait;

    /**
     * Nom de la table correspondant au modèle.
     *
     * @var string
     */
    protected string $table = "prt_artist_has_relationships";

    /**
     * Nom de la clé primaire de la table ou des clés composites.
     *
     * @var string|string[] ex: `uti_id` ou [`fk_uti_id`, `fk_role_id`]
     */
    protected string|array $pk = "id";

    /**
     * Tableau associatif d'attributs relatifs aux propriétés du modèle.
     *
     * @var array[]
     */
    protected array $attributes = [
        'id' => [
            'type' => 'int',
            'fillable' => false,
        ],
        'artist_id' => [
            'type' => 'int',
            'min' => 0,
            'max' => 4294967295,
        ],
        'release_id' => [
            'type' => 'int',
            'min' => 0,
            'max' => 4294967295,
        ],
        'prooject_id' => [
            'type' => 'int',
            'min' => 0,
            'max' => 4294967295,
        ],
        'movie_id' => [
            'type' => 'int',
            'min' => 0,
            'max' => 4294967295,
        ],
    ];

    /**
     * Renvoie la liste des tous les modèles enregistrés.
     *
     * @return \App\Entities\ArtistHasRelationship[]|mixed[]
     */
    public function all()
    {
        return $this->fields('id', 'artist_id', 'release_id', 'prooject_id', 'movie_id')
            ->order("artist_id ASC")->select();
    }
}
