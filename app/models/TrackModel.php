<?php

namespace App\Models;

use Core\AbstractModel;
use Core\ModelQueriesTrait;

class TrackModel extends AbstractModel
{
    use ModelQueriesTrait;

    /**
     * Nom de la table correspondant au modèle.
     *
     * @var string
     */
    protected string $table = "prt_tracks";

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
        'id_release' => [
            'type' => 'int',
            'min' => 0,
            'max' => 4294967295,
        ],
        'id_prooject' => [
            'type' => 'int',
            'min' => 0,
            'max' => 4294967295,
        ],
        'position' => [
            'type' => 'string',
            'maxLength' => 4,
        ],
        'bonus' => [
            'type' => 'int',
            'min' => 0,
            'max' => 255,
        ],
        'artist' => [
            'type' => 'string',
            'maxLength' => 100,
        ],
        'title' => [
            'type' => 'string',
            'maxLength' => 255,
        ],
        'url' => [
            'type' => 'string',
            'maxLength' => 255,
        ],
        'updated_at' => [
            'type' => 'datetime',
        ],
        'bandcamp_id' => [
            'type' => 'string',
            'maxLength' => 20,
        ],
    ];

    /**
     * Renvoie la liste des tous les modèles enregistrés.
     *
     * @return \App\Entities\Track[]|mixed[]
     */
    public function all()
    {
        return $this->fields('id', 'id_release', 'id_prooject', 'position', 'bonus', 'artist', 'title', 'url', 'updated_at', 'bandcamp_id')
            ->order("id_release ASC")->select();
    }
}
