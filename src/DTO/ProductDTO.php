<?php

namespace App\DTO;

class ProductDTO
{
    public function __construct(
        public string $name = '',
        public float $price = 0.0,
        public int $quantity = 0
    )
    {
    }

    public function getJson(): string
    {
        return json_encode($this);
    }
}