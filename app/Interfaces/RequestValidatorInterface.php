<?php

declare(strict_types=1);

namespace App\Interfaces;

use App\Enum\ActionMode;

interface RequestValidatorInterface
{
    public function validate(array $data, ActionMode $mode = ActionMode::Reg,array $files =[]): array;
}