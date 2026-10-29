<?php

namespace App\Entities;

use Core\AbstractDTO;

class Platform extends AbstractDTO
{
    public int $id;
    public ?string $name;
    public ?string $url;
    public string $slug;
}
