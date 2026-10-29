<?php

namespace App\Entities;

use Core\AbstractDTO;

class Product extends AbstractDTO
{
    public int $id;
    public string $ref;
    public string $artist;
    public string $title;
    public ?string $details;
    public ?string $description;
    public int $out_of_stock;
    public ?float $price;
    public ?string $purchase_url;
    public ?string $bandcamp_id;
    public ?string $created_at;
    public ?string $updated_at;
}
