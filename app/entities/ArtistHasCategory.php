<?php

namespace App\Entities;

use Core\AbstractDTO;

class ArtistHasCategory extends AbstractDTO
{
    public int $id;
    public int $artist_id;
    public int $category_id;
}
