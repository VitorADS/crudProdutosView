<?php

namespace App\HttpRequest;

class Development extends Base
{

    public function getUrl(): string
    {
        return 'https://localhost:8000';
    }
}