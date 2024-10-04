<?php

declare(strict_types=1);

namespace App\Enum;

enum ActionMode: string
{
    case Reg = 'reg';
    case Edit = 'edit';
    case Delete = 'delete';
    case Select = 'select';
}