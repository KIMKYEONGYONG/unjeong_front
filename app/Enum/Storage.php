<?php

declare(strict_types=1);

namespace App\Enum;

enum Storage: string
{
    case Local = 'local';
    case Remote = 'remote';
}