<?php

declare(strict_types=1);

namespace App\DataObjects;


class DenCodeData
{
    public function __construct(
        public readonly string $mode,
        public readonly string $key,
        public readonly string $iv,

    ) {
    }
}