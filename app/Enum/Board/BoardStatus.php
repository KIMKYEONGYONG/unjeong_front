<?php

declare(strict_types=1);

namespace App\Enum\Board;

enum BoardStatus: string
{
    case Active = '100';
    case Hind  = '900';

    case Close = '999';


    public function toString() : string
    {
        return match ($this) {
            self::Active => '노출',
            self::Hind => '미노출',
            self::Close => '종료',
        };
    }

}