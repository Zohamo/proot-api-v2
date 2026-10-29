<?php

namespace App\Entities;

use Core\AbstractDTO;

class ProductPicture extends AbstractDTO
{
    public int $id;
    public ?int $product_id;
    public string $thumb_url;
    public ?string $full_url;
}
