<?php

namespace App\HttpRequest\Interface;

use App\DTO\ProductDTO;
use Symfony\Contracts\HttpClient\ResponseInterface;

interface HttpRequestInterface
{
    public function getUrl(): string;
    public function sendRequest(string $endPoint, ?ProductDTO $productDTO = null): ResponseInterface;
}