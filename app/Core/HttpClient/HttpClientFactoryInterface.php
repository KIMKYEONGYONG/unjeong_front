<?php

declare(strict_types=1);

namespace App\Core\HttpClient;

interface HttpClientFactoryInterface
{
    public function make(array $option = []): HttpClientInterface;
}