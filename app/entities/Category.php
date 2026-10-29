<?php

namespace App\Entities;

use Core\AbstractDTO;

class Category extends AbstractDTO
{
    public int $id;
    public string $slug;
    public string $name;
    public ?string $icon;
}
