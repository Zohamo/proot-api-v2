<?php

namespace App\Entities;

use Core\AbstractDTO;

class Release extends AbstractDTO
{
    public int $id;
    public string $ref;
    public string $slug;
    public string $artist;
    public string $title;
    public ?string $description;
    public ?string $style;
    public int $disc_number;
    public ?string $bandcamp_id;
    public string $created_at;
    public ?string $updated_at;
}
