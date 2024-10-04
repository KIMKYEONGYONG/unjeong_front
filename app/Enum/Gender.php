<?php

declare(strict_types=1);

namespace App\Enum;

enum Gender: string
{
    case Male = 'm';
    case Female = 'f';

    public function toString() : string
    {
        return match($this){
            self::Male => '남자',
            self::Female => '여자',
        };
    }

}