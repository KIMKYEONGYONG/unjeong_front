<?php

declare(strict_types=1);

namespace App\Core;

use stdClass;

class JsonFormatter
{
    private static int $jsonFlags =  JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_APOS | JSON_THROW_ON_ERROR |JSON_UNESCAPED_UNICODE;

    public static function encode(mixed $data): string
    {
        return json_encode($data, self::$jsonFlags);
    }

    public static function decode(string $string,bool $isArray = true): array|stdClass
    {
        return json_decode($string, $isArray, 512, self::$jsonFlags);
    }
}