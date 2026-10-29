<?php

namespace App\Models;

use Core\AbstractModel;
use Core\ModelQueriesTrait;

class ReleaseLinkModel extends AbstractModel
{
    use ModelQueriesTrait;

    /**
     * Nom de la table correspondant au modèle.
     *
     * @var string
     */
    protected string $table = "prt_release_links";

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
        'id_platform' => [
            'type' => 'int',
            'min' => 0,
            'max' => 65535,
        ],
        'url' => [
            'type' => 'string',
            'maxLength' => 100,
        ],
    ];

    /**
     * Renvoie la liste des tous les modèles enregistrés.
     *
     * @return \App\Entities\ReleaseLink[]|mixed[]
     */
    public function all()
    {
        return $this->fields('id', 'id_release', 'id_platform', 'url')
            ->order("id_release ASC")->select();
    }
}
