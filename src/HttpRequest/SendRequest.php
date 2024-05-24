<?php

namespace App\HttpRequest;

use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

readonly class SendRequest
{
    /**
     * @throws TransportExceptionInterface
     */
    public function send(string $endPoint, array $params = []): ResponseInterface
    {
        $appEnv = $this->checkApplicationEnv();
        return $appEnv->sendRequest($endPoint, $params);
    }

    private function checkApplicationEnv(): Base
    {
        if(getenv('APP_ENV') !== 'prod') {
            return new Production();
        }

        return new Development();
    }

}