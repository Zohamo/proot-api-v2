<?php

namespace App\Models;

use Core\AbstractModel;
use Core\ModelQueriesTrait;

class CreditTypeModel extends AbstractModel
{
    use ModelQueriesTrait;

    /**
     * Nom de la table correspondant au modèle.
     *
     * @var string
     */
    protected string $table = "prt_credit_types";

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
        'label' => [
            'type' => 'string',
            'maxLength' => 20,
        ],
    ];

    /**
     * Renvoie la liste des tous les modèles enregistrés.
     *
     * @return \App\Entities\CreditType[]|mixed[]
     */
    public function all()
    {
        return $this->fields('id', 'label')
            ->order("label ASC")->select();
    }
}
