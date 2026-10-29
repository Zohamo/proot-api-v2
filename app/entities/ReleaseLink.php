<?php

namespace App\Entities;

use Core\AbstractDTO;

class ReleaseLink extends AbstractDTO
{
    public int $id;
    public int $id_release;
    public ?int $id_platform;
    public string $url;
}
