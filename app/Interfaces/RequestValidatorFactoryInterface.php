<?php

declare(strict_types=1);

namespace App\Interfaces;

interface RequestValidatorFactoryInterface
{
    public function make(string $class): RequestValidatorInterface;
}