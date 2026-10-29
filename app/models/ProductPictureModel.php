<?php

namespace App\Models;

use Core\AbstractModel;
use Core\ModelQueriesTrait;

class ProductPictureModel extends AbstractModel
{
    use ModelQueriesTrait;

    /**
     * Nom de la table correspondant au modèle.
     *
     * @var string
     */
    protected string $table = "prt_product_pictures";

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
        'product_id' => [
            'type' => 'int',
            'min' => 0,
            'max' => 65535,
        ],
        'thumb_url' => [
            'type' => 'string',
            'maxLength' => 255,
        ],
        'full_url' => [
            'type' => 'string',
            'maxLength' => 255,
        ],
    ];

    /**
     * Renvoie la liste des tous les modèles enregistrés.
     *
     * @return \App\Entities\ProductPicture[]|mixed[]
     */
    public function all()
    {
        return $this->fields('id', 'product_id', 'thumb_url', 'full_url')
            ->order("product_id ASC")->select();
    }
}
