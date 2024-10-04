<?php

declare(strict_types=1);

namespace App\Controllers\Action;

use App\Exception\ValidationException;
use App\Interfaces\RequestValidatorFactoryInterface;
use App\RequestValidators\SmsRequestValidator;
use App\Services\SmsService;
use Doctrine\ORM\Exception\ORMException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use ReflectionException;

class ActionSmsController
{
    public function __construct(
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly SmsService $service,
    ) {
    }

    /**
     * @throws ReflectionException | ORMException
     */
    public function register(Request $request, Response $response): Response
    {
        $data = $this->requestValidatorFactory->make(SmsRequestValidator::class)->validate(
            $request->getParsedBody()
        );
        $this->service->register($data);
        return $response;
    }

    /**
     * @throws ORMException
     */
    public function delete(Request $request, Response $response, array $args = []): Response
    {
        $id = isset($args['id'])? (int)$args['id'] : 0;
        if(!$board = $this->service->getById($id)){
            throw new ValidationException('Bad Request');
        }
        $this->service->delete($board);
        return $response;
    }
}