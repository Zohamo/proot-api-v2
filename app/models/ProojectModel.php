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
     * @return mixed[]
     */
    public function all()
    {
        $proojects = $this->fields('id', 'slug', 'title', 'DATE_FORMAT(created_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS createdAt', 'DATE_FORMAT(updated_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS updatedAt', 'bandcamp_id AS bandcampId', 'description', 'concept')
            ->order("updatedAt DESC")
            ->toArray()->select();
        foreach ($proojects as &$prooject) {
            $tracks = (new TrackModel())
                ->fields('id', 'position', 'bonus', 'artist', 'title', 'url', 'DATE_FORMAT(updated_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS updatedAt', 'bandcamp_id AS bandcampId')
                ->where('id_prooject = ?')
                ->order('CAST(position AS UNSIGNED) DESC')
                ->toArray()
                ->limit(3)
                ->select([$prooject['id']]);
            foreach ($tracks as &$track) {
                $resTrackInfo = (new TrackInfoModel())
                    ->fields('content')
                    ->where('id_track = ?')
                    ->toArray()
                    ->select([$track['id']]);
                $track['info'] = empty($resTrackInfo) ? null : $resTrackInfo[0];
            }
            $prooject['tracks'] = $tracks;
        }
        return $proojects;
    }

    /**
     * Renvoie le premier modèle trouvé, respectant les critères `$params`.
     *
     * @param  int|string|array $params
     * @return mixed[]|boolean Renvoie `false` si aucun modèle ne correspond aux critères demandés.
     */
    public function first(...$params)
    {
        $res = $this->fields('id', 'slug', 'title', 'DATE_FORMAT(created_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS createdAt', 'DATE_FORMAT(updated_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS updatedAt', 'bandcamp_id AS bandcampId', 'description', 'concept')
            ->where("slug = ?")
            ->limit(1)
            ->toArray()
            ->select([$params[0]]);
        if (empty($res)) {
            return false;
        }
        $prooject = $res[0];
        $tracks = (new TrackModel())
            ->fields('id', 'position', 'bonus', 'artist', 'title', 'url', 'DATE_FORMAT(updated_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS updatedAt', 'bandcamp_id AS bandcampId')
            ->where('id_prooject = ?')
            ->order('CAST(position AS UNSIGNED) ASC')
            ->toArray()
            ->select([$prooject['id']]);
        foreach ($tracks as &$track) {
            $resTrackInfo = (new TrackInfoModel())
                ->fields('content')
                ->where('id_track = ?')
                ->toArray()
                ->select([$track['id']]);
            $track['info'] = empty($resTrackInfo) ? null : $resTrackInfo[0];
        }
        $prooject['tracks'] = $tracks;
        return $prooject;
    }
}
