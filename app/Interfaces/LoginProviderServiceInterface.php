<?php

declare(strict_types=1);

namespace App\Interfaces;

interface LoginProviderServiceInterface
{
    public function getByCredentials(array $credentials): ?AuthUserInterface;

    public function getFindOneBy(array $criteria): ?AuthUserInterface;

}