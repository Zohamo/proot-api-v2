<?php

namespace App\Entities;

use Core\AbstractDTO;

class Track extends AbstractDTO
{
    public int $id;
    public ?int $id_release;
    public ?int $id_prooject;
    public ?string $position;
    public ?int $bonus;
    public ?string $artist;
    public ?string $title;
    public ?string $url;
    public ?string $updated_at;
    public ?string $bandcamp_id;
}
