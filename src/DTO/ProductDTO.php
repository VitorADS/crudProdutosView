<?php

namespace App\DTO;

class ProductDTO
{
    public function __construct(
        private ?int $id = null,
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

    public function getId(): ?int
    {
        return $this->id;
    }
}