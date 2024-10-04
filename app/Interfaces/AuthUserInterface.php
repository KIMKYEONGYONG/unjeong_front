<?php

declare(strict_types=1);

namespace App\Interfaces;

interface AuthUserInterface
{
    public function getId(): int;
    public function getUserId(): string;
    public function getName(): string;
    public function getPassword(): string;
}