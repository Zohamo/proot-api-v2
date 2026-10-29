<?php

namespace App\Models;

use Core\AbstractModel;
use Core\ModelQueriesTrait;

class ProductModel extends AbstractModel
{
    use ModelQueriesTrait;

    /**
     * Nom de la table correspondant au modèle.
     *
     * @var string
     */
    protected string $table = "prt_products";

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
        'artist' => [
            'type' => 'string',
            'maxLength' => 100,
        ],
        'title' => [
            'type' => 'string',
            'maxLength' => 255,
        ],
        'details' => [
            'type' => 'string',
            'maxLength' => 65535,
        ],
        'description' => [
            'type' => 'string',
            'maxLength' => 16777215,
        ],
        'out_of_stock' => [
            'type' => 'int',
            'min' => 0,
            'max' => 255,
        ],
        'price' => [
            'type' => 'float',
        ],
        'purchase_url' => [
            'type' => 'string',
            'maxLength' => 255,
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
     * @return \App\Entities\Product[]|mixed[]
     */
    public function all()
    {
        return $this->fields('id', 'ref', 'artist', 'title', 'details', 'description', 'out_of_stock', 'price', 'purchase_url', 'bandcamp_id', 'created_at', 'updated_at')
            ->order("created_at ASC")->select();
    }
}
