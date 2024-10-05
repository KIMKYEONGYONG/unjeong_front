<?php

declare(strict_types=1);

namespace App\Controllers\Action;

use App\Exception\ValidationException;
use App\Interfaces\AuthInterface;
use App\Interfaces\RequestValidatorFactoryInterface;
use App\RequestValidators\LoginRequestValidator;
use App\RequestValidators\UserRequestValidator;
use App\Services\AccountService;
use App\Services\HpCertificationService;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use ReflectionException;

class ActionAccountController
{
    public function __construct(
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly HpCertificationService $certificationService,
        private readonly AccountService $service,
    ) {
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     * @throws ReflectionException
     */
    public function register(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(UserRequestValidator::class)->validate(
            $request->getParsedBody()
        );
        $this->certificationService->phoneForAuthNoRemoveAll($data['phone']);
        $this->service->register($data);
        return $response;
    }
}