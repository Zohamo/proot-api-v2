<?php

namespace App\Models;

use Core\AbstractModel;
use Core\ModelQueriesTrait;

class PlatformModel extends AbstractModel
{
    use ModelQueriesTrait;

    /**
     * Nom de la table correspondant au modèle.
     *
     * @var string
     */
    protected string $table = "prt_platforms";

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
        'name' => [
            'type' => 'string',
            'maxLength' => 100,
        ],
        'url' => [
            'type' => 'string',
            'maxLength' => 100,
        ],
        'slug' => [
            'type' => 'string',
            'maxLength' => 50,
        ],
    ];

    /**
     * Renvoie la liste des tous les modèles enregistrés.
     *
     * @return \App\Entities\Platform[]|mixed[]
     */
    public function all()
    {
        return $this->fields('id', 'name', 'url', 'slug')
            ->order("name ASC")->select();
    }
}
