<?php

namespace App\Models;

use Core\AbstractModel;
use Core\ModelQueriesTrait;

class ArtistHasCategoryModel extends AbstractModel
{
    use ModelQueriesTrait;

    /**
     * Nom de la table correspondant au modèle.
     *
     * @var string
     */
    protected string $table = "prt_artist_has_categories";

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
        'artist_id' => [
            'type' => 'int',
            'min' => -2147483648,
            'max' => 2147483647,
        ],
        'category_id' => [
            'type' => 'int',
            'min' => -2147483648,
            'max' => 2147483647,
        ],
    ];

    /**
     * Renvoie la liste des tous les modèles enregistrés.
     *
     * @return \App\Entities\ArtistHasCategory[]|mixed[]
     */
    public function all()
    {
        return $this->fields('id', 'artist_id', 'category_id')
            ->order("artist_id ASC")->select();
    }
}
