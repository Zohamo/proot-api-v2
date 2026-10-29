<?php

namespace App\Models;

use Core\AbstractModel;

class ProductModel extends AbstractModel
{
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
     * @return mixed[]
     */
    public function all()
    {
        $products = $this->fields('id', 'artist', 'title', 'details', 'description', 'price', 'purchase_url AS purchaseUrl', 'bandcamp_id AS bandcampId', 'DATE_FORMAT(created_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS createdAt', 'DATE_FORMAT(updated_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS updatedAt')
            ->where("out_of_stock = 0")
            ->order("created_at DESC")
            ->toArray()
            ->select();
        foreach ($products as &$product) {
            $product['pictures'] = (new ProductPictureModel())
                ->fields('id', 'thumb_url AS thumbUrl', 'full_url AS fullUrl')
                ->where('product_id = ?')
                ->toArray()
                ->select([$product['id']]);
        }
        return $products;
    }
}
