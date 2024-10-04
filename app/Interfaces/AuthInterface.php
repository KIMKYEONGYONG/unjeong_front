<?php

declare(strict_types=1);

namespace App\Interfaces;

interface AuthInterface
{
    public function authUser(): ?AuthUserInterface;

    public function attemptLogin(array $credentials): bool;

    public function checkCredentials(AuthUserInterface $user, array $credentials): bool;

    public function logOut(): void;

    public function logIn(AuthUserInterface $authUser): void;
}