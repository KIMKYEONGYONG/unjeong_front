<?php

declare(strict_types=1);

namespace App\Traits;

use Psr\Http\Message\ServerRequestInterface;

trait DataProcessTrait
{
    public function getRequestData(ServerRequestInterface $request): array
    {
        return (strtoupper($request->getMethod()) === 'POST') ? (array) $request->getParsedBody() : $request->getQueryParams();
    }

    public function dataKeyExistsAndSetDefault(string $key,array $array ,string|int $defaultValue) : string | int
    {
        $value =  (array_key_exists($key, $array)) ? $array[$key] : $defaultValue;

        return is_int($defaultValue) ? (int) $value : $value;
    }

}