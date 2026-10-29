<?php

namespace App\Entities;

use Core\AbstractDTO;

class ReleaseCredit extends AbstractDTO
{
    public int $id;
    public int $id_release;
    public int $id_credit_type;
    public string $content;
}
