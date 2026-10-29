<?php

namespace App\Entities;

use Core\AbstractDTO;

class Prooject extends AbstractDTO
{
    public int $id;
    public string $slug;
    public ?string $title;
    public ?string $created_at;
    public ?string $updated_at;
    public ?string $bandcamp_id;
    public ?string $description;
    public ?string $concept;
}
