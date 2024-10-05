<?php

declare(strict_types=1);

namespace App\Core\HttpClient;

use Psr\Http\Message\ResponseInterface;

interface HttpClientInterface
{
    public function withOption(array $option = []): HttpClientInterface;
    public function request(string $method, string $url, array $options = []): ResponseInterface;
}