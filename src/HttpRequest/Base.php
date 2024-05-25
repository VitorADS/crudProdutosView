<?php

Namespace App\HttpRequest;

use App\DTO\ProductDTO;
use App\HttpRequest\Interface\HttpRequestInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

abstract class Base implements HttpRequestInterface
{
    private readonly HttpClientInterface $client;

    //EndPoints
    public const string findProducts = '/api/product';
    public const string createProduct = '/api/product/create';
    public const string updateProduct = '/api/product/edit/';
    public const string deleteProduct = '/api/product/remove/';

    public function __construct()
    {
        $this->client = HttpClient::create([
            'headers' => [
                'content-type' => 'application/json'
            ]
        ]);
    }

    public function sendRequest(string $endPoint, ?ProductDTO $productDTO = null): ResponseInterface
    {
        switch ($endPoint){
            case self::findProducts:
                return $this->sendFindProducts();
                break;
            case self::createProduct:
                return $this->sendCreateProduct($productDTO);
                break;
            case self::updateProduct:
                return $this->sendUpdateProduct($productDTO);
                break;
            case self::deleteProduct:
                return $this->sendRemoveProduct($productDTO->getId());
                break;
        }
    }

    protected function sendFindProducts(): ResponseInterface
    {
        return $this->client->request('GET', $this->getUrl() . self::findProducts);
    }

    protected function sendCreateProduct(ProductDTO $productDTO): ResponseInterface
    {
        return $this->client->request('POST', $this->getUrl() . self::createProduct, [
            'json' => $productDTO
        ]);
    }

    protected function sendUpdateProduct(ProductDTO $productDTO): ResponseInterface
    {
        return $this->client->request('PUT', $this->getUrl() . self::updateProduct . $productDTO->getId(), [
            'json' => $productDTO
        ]);
    }

    protected function sendRemoveProduct(int $idProduct): ResponseInterface
    {
        return $this->client->request('DELETE', $this->getUrl() . self::deleteProduct . $idProduct);
    }
}