<?php

declare(strict_types=1);

namespace App\Controllers\Action;

use App\Exception\ValidationException;
use App\Interfaces\AuthInterface;
use App\Interfaces\RequestValidatorFactoryInterface;
use App\RequestValidators\LoginRequestValidator;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ActionAuthController
{
    public function __construct(
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly AuthInterface $auth
    ) {
    }

    public function login(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(LoginRequestValidator::class)->validate(
            $request->getParsedBody()
        );
        if (! $this->auth->attemptLogin($data)) {
            throw new ValidationException(['로그인 정보가 없습니다']);
        }
        return $response;
    }
}