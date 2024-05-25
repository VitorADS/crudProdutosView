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
    public const FIND_PRODUCTS = '/api/product';
    public const CREATE_PRODUCT = '/api/product/create';
    public const UPDATE_PRODUCT = '/api/product/edit/';
    public const DELETE_PRODUCT = '/api/product/remove/';

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
            case self::FIND_PRODUCTS:
                return $this->sendFIND_PRODUCTS();
                break;
            case self::CREATE_PRODUCT:
                return $this->sendCREATE_PRODUCT($productDTO);
                break;
            case self::UPDATE_PRODUCT:
                return $this->sendUPDATE_PRODUCT($productDTO);
                break;
            case self::DELETE_PRODUCT:
                return $this->sendRemoveProduct($productDTO->getId());
                break;
        }
    }

    protected function sendFIND_PRODUCTS(): ResponseInterface
    {
        return $this->client->request('GET', $this->getUrl() . self::FIND_PRODUCTS);
    }

    protected function sendCREATE_PRODUCT(ProductDTO $productDTO): ResponseInterface
    {
        return $this->client->request('POST', $this->getUrl() . self::CREATE_PRODUCT, [
            'json' => $productDTO
        ]);
    }

    protected function sendUPDATE_PRODUCT(ProductDTO $productDTO): ResponseInterface
    {
        return $this->client->request('PUT', $this->getUrl() . self::UPDATE_PRODUCT . $productDTO->getId(), [
            'json' => $productDTO
        ]);
    }

    protected function sendRemoveProduct(int $idProduct): ResponseInterface
    {
        return $this->client->request('DELETE', $this->getUrl() . self::DELETE_PRODUCT . $idProduct);
    }
}