<?php

namespace App\HttpRequest;

class Production extends Base
{

    public function getUrl(): string
    {
        return 'https://apiprodutos.vitorads.com.br';
    }
}