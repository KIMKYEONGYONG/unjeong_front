<?php

declare(strict_types=1);

namespace App\Core\Helpers;

class DateHelper
{

    public static function getToday() : string
    {
        return date('Y-m-d');
    }

    public static function getDateYmd(string $day) : string
    {
        return date("Y-m-d", strtotime($day.' days'));
    }

}