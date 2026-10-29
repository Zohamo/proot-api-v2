<?php

namespace App\Models;

use Core\AbstractModel;

class MovieModel extends AbstractModel
{
    /**
     * Nom de la table correspondant au modèle.
     *
     * @var string
     */
    protected string $table = "prt_movies";

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
            'maxLength' => 255,
        ],
        'title' => [
            'type' => 'string',
            'maxLength' => 100,
        ],
        'artist' => [
            'type' => 'string',
            'maxLength' => 100,
        ],
        'embed' => [
            'type' => 'string',
            'maxLength' => 1024,
        ],
        'description' => [
            'type' => 'string',
            'maxLength' => 65535,
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
        return $this->fields('id', 'slug', 'title', 'artist', 'embed', 'description', 'DATE_FORMAT(created_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS createdAt', 'DATE_FORMAT(updated_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS updatedAt')
            ->order("createdAt DESC")
            ->toArray()->select();
    }

    /**
     * Renvoie le premier modèle trouvé, respectant les critères `$params`.
     *
     * @param  int|string|array $params
     * @return mixed[]|boolean Renvoie `false` si aucun modèle ne correspond aux critères demandés.
     */
    public function first(...$params)
    {
        $res = $this->fields('id', 'slug', 'title', 'artist', 'embed', 'description', 'DATE_FORMAT(created_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS createdAt', 'DATE_FORMAT(updated_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS updatedAt')
            ->where("slug = ?")
            ->limit(1)
            ->toArray()
            ->select([$params[0]]);
        return empty($res) ? false : $res[0];
    }
}
