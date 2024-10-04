<?php

declare(strict_types=1);

namespace App\Core;

use Closure;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Csrf
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly ResponseFormatter $responseFormatter
    ){
    }

    public function failureHandler(): Closure
    {
        return fn(
            ServerRequestInterface $request,
            RequestHandlerInterface $handler
        ) =>    $this->responseFormatter->asJson(
                    $this->responseFactory
                        ->createResponse()
                        ->withStatus(403),
                    ['message'=>'Failed CSRF check!']
                );

    }
}