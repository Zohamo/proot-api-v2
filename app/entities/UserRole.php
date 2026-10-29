<?php

namespace App\Entities;

use Core\AbstractDTO;

class UserRole extends AbstractDTO
{
    public int $rol_id = 1;
    public ?string $rol_label = "";
    public ?string $rol_description = "";
}
