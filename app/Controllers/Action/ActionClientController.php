<?php

declare(strict_types=1);

namespace App\Controllers\Action;

use App\Core\ResponseFormatter;
use App\DataObjects\ResponseData;
use App\Interfaces\RequestValidatorFactoryInterface;
use App\RequestValidators\ClientRequestValidator;
use App\Services\ClientService;
use Doctrine\ORM\Exception\ORMException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use ReflectionException;

class ActionClientController
{
    public function __construct(
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly ClientService $service,
    ) {
    }

    /**
     * @throws ReflectionException | ORMException
     */
    public function register(Request $request, Response $response): Response
    {
        $body = $request->getParsedBody() ?? [];
        $data = $this->requestValidatorFactory->make(ClientRequestValidator::class)->validate(
            $body
        );
        $this->service->register($data);
        return $response;
    }
}