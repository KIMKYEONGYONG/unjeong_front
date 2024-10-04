<?php

declare(strict_types=1);

namespace App\Helper;

use App\Core\Config;
use App\Core\DenCode;
use App\Core\JsonFormatter;
use App\DataObjects\DenCodeData;

class ReadFileHelper
{
    public static function getFileCode(array $filePath): string
    {
        $denCode = self::getDenCode();
        return DenCode::encrypt(JsonFormatter::encode($filePath), $denCode->mode, $denCode->key, hex2bin($denCode->iv));
    }

    public static function getFilePath(string $code): array
    {
        $denCode = self::getDenCode();
        $filePath = DenCode::decrypt($code, $denCode->mode, $denCode->key, hex2bin($denCode->iv));
        return JsonFormatter::decode($filePath);
    }


    public static function getDenCode() : DenCodeData
    {
        $config =  new Config(require CONFIG_PATH. '/settings.php');
        return new DenCodeData(
            $config->get('file.mode'),
            $config->get('file.key'),
            $config->get('file.iv'),
        );
    }

}