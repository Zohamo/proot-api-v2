<?php

namespace App\Entities;

use Core\AbstractDTO;

class TrackInfo extends AbstractDTO
{
    public int $id;
    public ?int $id_track;
    public ?string $content;
}
