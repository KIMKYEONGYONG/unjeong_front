<?php

declare(strict_types=1);

namespace App\Enum\Client;

enum ClientAge: int
{
    case Twenty = 20;
    case Thirty = 30;
    case Forty = 40;
    case Fifty = 50;
    case Sixty = 60;

    public function toString() : string
    {
        return match($this){
            self::Twenty => "20대",
            self::Thirty => "30대",
            self::Forty => "40대",
            self::Fifty => "50대",
            self::Sixty => "60대 이상",
        };
    }
}