<?php

namespace App\Models;

use Core\AbstractModel;
use Core\ModelQueriesTrait;

class ProojectModel extends AbstractModel
{
    use ModelQueriesTrait;

    /**
     * Nom de la table correspondant au modèle.
     *
     * @var string
     */
    protected string $table = "prt_proojects";

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
        'title' => [
            'type' => 'string',
            'maxLength' => 100,
        ],
        'created_at' => [
            'type' => 'datetime',
        ],
        'updated_at' => [
            'type' => 'datetime',
        ],
        'bandcamp_id' => [
            'type' => 'string',
            'maxLength' => 20,
        ],
        'description' => [
            'type' => 'string',
            'maxLength' => 16777215,
        ],
        'concept' => [
            'type' => 'string',
            'maxLength' => 16777215,
        ],
    ];

    /**
     * Renvoie la liste des tous les modèles enregistrés.
     *
     * @return \App\Entities\Prooject[]|mixed[]
     */
    public function all()
    {
        return $this->fields('id', 'slug', 'title', 'created_at', 'updated_at', 'bandcamp_id', 'description', 'concept')
            ->order("title ASC")->select();
    }
}
