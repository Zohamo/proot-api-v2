<?php

namespace App\Entities;

use Core\AbstractDTO;

class ArtistHasRelationship extends AbstractDTO
{
    public int $id;
    public int $artist_id;
    public ?int $release_id;
    public ?int $prooject_id;
    public ?int $movie_id;
}
