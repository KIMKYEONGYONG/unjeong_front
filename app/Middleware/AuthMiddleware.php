<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\ResponseFormatter;
use App\Interfaces\AuthInterface;
use App\Interfaces\SessionInterface;
use App\Services\RequestService;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Slim\Views\Twig;

class AuthMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly ResponseFactoryInterface $responseFactory,
        private readonly AuthInterface $auth,
        private readonly Twig $twig,
        private readonly RequestService $requestService,
        private readonly SessionInterface $session,
        private readonly ResponseFormatter $responseFormatter,
    ){
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($user = $this->auth->authUser()) {
            $this->twig->getEnvironment()->addGlobal('auth', ['name' => $user->getName()]);
            return $handler->handle($request->withAttribute('user', $user));
        }
        if ($this->session->get('user')) {
            $this->auth->logOut();
        }
        return $this->requestService->isXhr($request) ?
            $this->responseFormatter->asJson(
                $this->responseFactory->createResponse(401),
                [
                    'message' => '세션이 종료 되었습니다 다시 로그인 하시기 바랍니다'
                ]
            ) :
            $this->responseFactory->createResponse(302)->withHeader('Location', '/auth/login');
    }
}