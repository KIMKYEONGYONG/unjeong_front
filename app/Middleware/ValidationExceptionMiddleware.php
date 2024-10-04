<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\ResponseFormatter;
use App\Exception\ValidationException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class ValidationExceptionMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly ResponseFormatter $responseFormatter

    ){
    }
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (ValidationException $e) {
            $errorMessage = '';
            if (is_array($e->errors)) {
                foreach ($e->errors as $error) {
                    if (!empty($error[0])) {
                        if (is_array($error)) {
                            $errorMessage = $error[0];
                        }
                        else {
                            $errorMessage = $error;
                        }
                        break;
                    }
                }
            }
            else {
                $errorMessage = $e->errors;
            }

            $response = $this->responseFactory->createResponse()->withStatus($e->getCode());
            return $this->responseFormatter->asJson($response,['message' => $errorMessage]);
        }
    }
}