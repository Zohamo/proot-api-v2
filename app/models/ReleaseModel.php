<?php

namespace App\Models;

use Core\AbstractModel;

class ReleaseModel extends AbstractModel
{
    /**
     * Nom de la table correspondant au modèle.
     *
     * @var string
     */
    protected string $table = "prt_releases";

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
        'slug' => [
            'type' => 'string',
            'maxLength' => 255,
        ],
        'artist' => [
            'type' => 'string',
            'maxLength' => 100,
        ],
        'title' => [
            'type' => 'string',
            'maxLength' => 255,
        ],
        'description' => [
            'type' => 'string',
            'maxLength' => 16777215,
        ],
        'style' => [
            'type' => 'string',
            'maxLength' => 100,
        ],
        'disc_number' => [
            'type' => 'int',
            'default' => '1',
            'min' => 0,
            'max' => 255,
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
        return $this->fields('ref', 'slug', 'artist', 'title', 'style', 'disc_number AS discNumber', 'bandcamp_id AS bandcampId', 'DATE_FORMAT(created_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS createdAt', 'DATE_FORMAT(updated_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS updatedAt')
            ->order("created_at DESC")
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
        $res = $this->fields('id', 'ref', 'slug', 'artist', 'title', 'description', 'style', 'disc_number AS discNumber', 'bandcamp_id AS bandcampId', 'DATE_FORMAT(created_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS createdAt', 'DATE_FORMAT(updated_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS updatedAt')
            ->where("ref = ?")
            ->limit(1)
            ->toArray()
            ->select([$params[0]]);
        if (empty($res)) {
            return false;
        }
        $release = $res[0];
        $release['tracks'] = $this->getReleaseTracks($release['id']);
        $release['credits'] = $this->getReleaseCredits($release['id']);
        $release['links'] = $this->getReleaseLinks($release['id']);
        return $release;
    }

    /**
     * Renvoie le dernier modèle créé.
     *
     * @return mixed[]|boolean Renvoie `false` si aucun modèle ne correspond aux critères demandés.
     */
    public function last()
    {
        $res = $this->fields('id', 'ref', 'slug', 'artist', 'title', 'description', 'style', 'disc_number AS discNumber', 'bandcamp_id AS bandcampId', 'DATE_FORMAT(created_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS createdAt', 'DATE_FORMAT(updated_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS updatedAt')
            ->order("created_at DESC")
            ->limit(1)
            ->toArray()
            ->select();
        if (empty($res)) {
            return false;
        }
        $release = $res[0];
        $release['links'] = $this->getReleaseLinks($release['id']);
        return $release;
    }

    /**
     * Renvoie les pistes associées à une release donnée.
     *
     * @param  int $releaseId
     * @return mixed[] Tableau associatif contenant les pistes associées à la release.
     */
    private function getReleaseTracks(int $releaseId): array
    {
        $tracks = (new TrackModel())
            ->fields('id', 'position', 'bonus', 'artist', 'title', 'url', 'DATE_FORMAT(updated_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS updatedAt', 'bandcamp_id AS bandcampId')
            ->where('id_release = ?')
            ->order('CAST(position AS UNSIGNED) ASC')
            ->toArray()
            ->select([$releaseId]);
        foreach ($tracks as &$track) {
            $resTrackInfo = (new TrackInfoModel())
                ->fields('content')
                ->where('id_track = ?')
                ->toArray()
                ->select([$track['id']]);
            $track['info'] = empty($resTrackInfo) ? null : $resTrackInfo[0];
        }
        return $tracks;
    }

    /**
     * Renvoie les crédits associés à une release donnée.
     *
     * @param  int $releaseId
     * @return mixed[] Tableau associatif contenant les crédits associés à la release.
     */
    private function getReleaseCredits(int $releaseId): array
    {
        $credits = (new ReleaseCreditModel())
            ->fields('id', 'id_credit_type', 'content')
            ->where('id_release = ?')
            ->order('id_credit_type ASC')
            ->toArray()
            ->select([$releaseId]);
        foreach ($credits as &$credit) {
            $resCreditType = (new CreditTypeModel())
                ->fields('id', 'label')
                ->where('id = ?')
                ->toArray()
                ->select([$credit['id_credit_type']]);
            $credit['type'] = empty($resCreditType) ? null : $resCreditType[0];
            unset($credit['id_credit_type']);
        }
        return $credits;
    }

    /**
     * Renvoie les liens associés à une release donnée.
     *
     * @param  int $releaseId
     * @return mixed[] Tableau associatif contenant les liens associés à la release.
     */
    private function getReleaseLinks(int $releaseId): array
    {
        $links = (new ReleaseLinkModel())
            ->fields('id_platform', 'url')
            ->where('id_release = ?')
            ->order('id_platform ASC')
            ->toArray()
            ->select([$releaseId]);
        foreach ($links as &$link) {
            $resPlatform = (new PlatformModel())
                ->fields('name', 'url', 'slug')
                ->where('id = ?')
                ->toArray()
                ->select([$link['id_platform']]);
            $link['platform'] = empty($resPlatform) ? null : $resPlatform[0];
            unset($link['id_platform']);
        }
        return $links;
    }
}
