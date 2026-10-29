<?php

namespace App\Entities;

use Core\AbstractDTO;

class Movie extends AbstractDTO
{
    public int $id;
    public string $slug;
    public string $title;
    public ?string $artist;
    public ?string $embed;
    public ?string $description;
    public ?string $created_at;
    public ?string $updated_at;
}
