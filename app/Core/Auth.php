<?php

declare(strict_types=1);

namespace App\Core;

use App\Interfaces\AuthInterface;
use App\Interfaces\LoginProviderServiceInterface;
use App\Interfaces\SessionInterface;
use App\Interfaces\AuthUserInterface;

class Auth implements AuthInterface
{

    private ?AuthUserInterface $authUser = null;

    public function __construct(
        private readonly LoginProviderServiceInterface $loginProvider,
        private readonly SessionInterface $session
    ){
    }

    public function authUser(): ?AuthUserInterface
    {
        if ($this->authUser !== null) {
            return $this->authUser;
        }

        $id = $this->session->get('user');

        if (!$id) {
            return null;
        }
        $authUser = $this->loginProvider->getFindOneBy(['id' => $id]);

        if (! $authUser) {
            return null;
        }

        $this->authUser = $authUser;

        return $this->authUser;
    }

    public function attemptLogin(array $credentials): bool
    {
        $user = $this->loginProvider->getByCredentials($credentials);
        if (! $user || ! $this->checkCredentials($user,$credentials)) {
            return false;
        }

        $this->logIn($user);

        return true;
    }

    public function checkCredentials(AuthUserInterface $user, array $credentials): bool
    {
        return password_verify($credentials['password'], $user->getPassword());
    }

    public function logOut(): void
    {
        $this->session->forget('user');
        $this->session->regenerate();

        $this->authUser = null;
    }

    public function logIn(AuthUserInterface $authUser): void
    {
        $this->session->regenerate();
        $this->session->put('user', $authUser->getId());
        $this->authUser = $authUser;
    }
}