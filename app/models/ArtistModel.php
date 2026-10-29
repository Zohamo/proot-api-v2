<?php

namespace App\Models;

use Core\AbstractModel;
use Core\ModelQueriesTrait;

class ArtistModel extends AbstractModel
{
    use ModelQueriesTrait;

    /**
     * Nom de la table correspondant au modèle.
     *
     * @var string
     */
    protected string $table = "prt_artists";

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
        'slug' => [
            'type' => 'string',
            'maxLength' => 100,
        ],
        'name' => [
            'type' => 'string',
            'maxLength' => 50,
        ],
        'url' => [
            'type' => 'string',
            'maxLength' => 255,
        ],
        'description' => [
            'type' => 'string',
            'maxLength' => 65535,
        ],
    ];

    /**
     * Renvoie la liste des tous les modèles enregistrés.
     *
     * @return \App\Entities\Artist[]|mixed[]
     */
    public function all()
    {
        return $this->fields('id', 'slug', 'name', 'url', 'description')
            ->order("name ASC")->select();
    }
}
