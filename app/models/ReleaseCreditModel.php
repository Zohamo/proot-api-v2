<?php

namespace App\Models;

use Core\AbstractModel;
use Core\ModelQueriesTrait;

class ReleaseCreditModel extends AbstractModel
{
    use ModelQueriesTrait;

    /**
     * Nom de la table correspondant au modèle.
     *
     * @var string
     */
    protected string $table = "prt_release_credits";

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
        'id_credit_type' => [
            'type' => 'int',
            'min' => 0,
            'max' => 4294967295,
        ],
        'content' => [
            'type' => 'string',
            'maxLength' => 16777215,
        ],
    ];

    /**
     * Renvoie la liste des tous les modèles enregistrés.
     *
     * @return \App\Entities\ReleaseCredit[]|mixed[]
     */
    public function all()
    {
        return $this->fields('id', 'id_release', 'id_credit_type', 'content')
            ->order("id_release ASC")->select();
    }
}
