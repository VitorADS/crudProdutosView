<?php

namespace App\HttpRequest;

use App\DTO\ProductDTO;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

readonly class SendRequest
{
    /**
     * @throws TransportExceptionInterface
     */
    public function send(string $endPoint, ?ProductDTO $productDTO = null): ResponseInterface
    {
        $appEnv = $this->checkApplicationEnv();
        return $appEnv->sendRequest($endPoint, $productDTO);
    }

    private function checkApplicationEnv(): Base
    {
        if(getenv('APP_ENV') !== 'prod') {
            return new Production();
        }

        return new Development();
    }

}