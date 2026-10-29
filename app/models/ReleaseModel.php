<?php

namespace App\Models;

use Core\AbstractModel;
use Core\ModelQueriesTrait;

class ReleaseModel extends AbstractModel
{
    use ModelQueriesTrait;

    /**
     * Nom de la table correspondant au modèle.
     *
     * @var string
     */
    protected string $table = "prt_releases";

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
        'ref' => [
            'type' => 'string',
            'maxLength' => 20,
        ],
        'slug' => [
            'type' => 'string',
            'maxLength' => 255,
        ],
        'artist' => [
            'type' => 'string',
            'maxLength' => 100,
        ],
        'title' => [
            'type' => 'string',
            'maxLength' => 255,
        ],
        'description' => [
            'type' => 'string',
            'maxLength' => 16777215,
        ],
        'style' => [
            'type' => 'string',
            'maxLength' => 100,
        ],
        'disc_number' => [
            'type' => 'int',
            'default' => '1',
            'min' => 0,
            'max' => 255,
        ],
        'bandcamp_id' => [
            'type' => 'string',
            'maxLength' => 20,
        ],
        'created_at' => [
            'type' => 'datetime',
        ],
        'updated_at' => [
            'type' => 'datetime',
        ],
    ];

    /**
     * Renvoie la liste des tous les modèles enregistrés.
     *
     * @return \App\Entities\Release[]|mixed[]
     */
    public function all()
    {
        return $this->fields('id', 'ref', 'slug', 'artist', 'title', 'description', 'style', 'disc_number', 'bandcamp_id', 'created_at', 'updated_at')
            ->order("created_at ASC")->select();
    }
}
