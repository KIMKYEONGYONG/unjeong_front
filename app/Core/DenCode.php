<?php

declare(strict_types=1);

namespace App\Core;

class DenCode
{
    public static function encrypt($str, $mode, $key, $iv) : string|bool
    {
        $encrypted = openssl_encrypt($str, $mode, $key, OPENSSL_RAW_DATA, $iv);
        return base64_encode($encrypted);
    }

    public static function decrypt($str, $mode, $key, $iv) : string|bool
    {
        $text = base64_decode($str);
        return openssl_decrypt($text, $mode, $key, OPENSSL_RAW_DATA, $iv);
    }

}