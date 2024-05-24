<?php

Namespace App\HttpRequest;

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

    public function __construct()
    {
        $this->client = HttpClient::create([
            'headers' => [
                'content-type' => 'application/json'
            ]
        ]);
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function sendRequest(string $endPoint, array $params = []): ResponseInterface
    {
        switch ($endPoint){
            case self::findProducts:
                return $this->sendFindProducts();
                break;
            default:
                return $this->sendFindProducts();
                break;
        }
    }

    /**
     * @throws TransportExceptionInterface
     */
    protected function sendFindProducts(): ResponseInterface
    {
        return $this->client->request('GET', $this->getUrl() . self::findProducts);
    }
}