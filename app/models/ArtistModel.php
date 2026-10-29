<?php

namespace App\Models;

use Core\AbstractModel;

class ArtistModel extends AbstractModel
{
    /**
     * Nom de la table correspondant au modèle.
     *
     * @var string
     */
    protected string $table = "prt_artists";

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
        'name' => [
            'type' => 'string',
            'maxLength' => 50,
        ],
        'url' => [
            'type' => 'string',
            'maxLength' => 255,
        ],
        'description' => [
            'type' => 'string',
            'maxLength' => 65535,
        ],
    ];

    /**
     * Renvoie la liste des tous les modèles enregistrés.
     *
     * @return mixed[]
     */
    public function all()
    {
        $artists = $this->fields('id', 'slug', 'name', 'url', 'description')
            ->order("name ASC")
            ->toArray()->select();
        foreach ($artists as &$artist) {
            $artist['categories'] = (new ArtistHasCategoryModel())
                ->fields('prt_categories.*')
                ->leftJoin('prt_categories', 'prt_categories.id = prt_artist_has_categories.category_id')
                ->where('artist_id = ?')
                ->toArray()
                ->select([$artist['id']]);
        }
        return $artists;
    }

    /**
     * Renvoie le premier modèle trouvé, respectant les critères `$params`.
     *
     * @param  int|string|array $params
     * @return mixed[]|boolean Renvoie `false` si aucun modèle ne correspond aux critères demandés.
     */
    public function first(...$params)
    {
        $res = $this->fields('id', 'slug', 'name', 'url', 'description')
            ->where("slug = ?")
            ->limit(1)
            ->toArray()
            ->select([$params[0]]);
        if (empty($res)) {
            return false;
        }
        $artist = $res[0];

        $artist['categories'] = (new ArtistHasCategoryModel())
            ->fields('prt_categories.id', 'prt_categories.slug', 'prt_categories.name', 'prt_categories.icon')
            ->leftJoin('prt_categories', 'prt_categories.id = prt_artist_has_categories.category_id')
            ->where('artist_id = ?')
            ->toArray()
            ->select([$artist['id']]);
        $artist['relationships']['releases'] = (new ArtistHasRelationshipModel())
            ->fields('prt_releases.id', 'prt_releases.ref', 'prt_releases.slug', 'prt_releases.artist', 'prt_releases.title', 'prt_releases.description', 'prt_releases.style', 'prt_releases.disc_number AS discNumber', 'prt_releases.bandcamp_id AS bandcampId', 'DATE_FORMAT(prt_releases.created_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS createdAt', 'DATE_FORMAT(prt_releases.updated_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS updatedAt')
            ->leftJoin('prt_releases', 'prt_releases.id = prt_artist_has_relationships.release_id')
            ->where('artist_id = ? AND prt_artist_has_relationships.release_id IS NOT NULL')
            ->order('prt_releases.updated_at DESC')
            ->toArray()
            ->select([$artist['id']]);
        $artist['relationships']['proojects'] = (new ArtistHasRelationshipModel())
            ->fields('prt_proojects.id', 'prt_proojects.slug', 'prt_proojects.title', 'DATE_FORMAT(prt_proojects.created_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS createdAt', 'DATE_FORMAT(prt_proojects.updated_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS updatedAt', 'prt_proojects.bandcamp_id AS bandcampId', 'prt_proojects.description', 'prt_proojects.concept')
            ->leftJoin('prt_proojects', 'prt_proojects.id = prt_artist_has_relationships.prooject_id')
            ->where('artist_id = ? AND prt_artist_has_relationships.prooject_id IS NOT NULL')
            ->toArray()
            ->select([$artist['id']]);
        $artist['relationships']['videos'] = (new ArtistHasRelationshipModel())
            ->fields('prt_movies.id', 'prt_movies.slug', 'prt_movies.title', 'prt_movies.artist', 'prt_movies.embed', 'prt_movies.description', 'DATE_FORMAT(prt_movies.created_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS createdAt', 'DATE_FORMAT(prt_movies.updated_at, "%Y-%m-%dT%H:%i:%s.%fZ") AS updatedAt')
            ->leftJoin('prt_movies', 'prt_movies.id = prt_artist_has_relationships.movie_id')
            ->where('artist_id = ? AND prt_artist_has_relationships.movie_id IS NOT NULL')
            ->order('prt_movies.updated_at DESC')
            ->toArray()
            ->select([$artist['id']]);
        return $artist;
    }
}
