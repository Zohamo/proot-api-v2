<?php

namespace App\Entities;

use Core\AbstractDTO;

class Artist extends AbstractDTO
{
    public int $id;
    public string $slug;
    public string $name;
    public ?string $url;
    public ?string $description;
}
