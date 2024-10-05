<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Interfaces\SessionInterface;
use App\Services\RequestService;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class GuestMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly SessionInterface $session,
        private readonly RequestService $requestService
    ) {
    }
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->session->get('user')) {
            if ($this->requestService->isXhr($request)) {
                return $this->responseFactory->createResponse(422);
            }
            return $this->responseFactory->createResponse(302)->withHeader('Location', '/menu8/member_notice/list');
        }

        return $handler->handle($request);
    }
}