<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Config;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;
use Slim\Views\Twig;

class RequestMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Twig $twig,
        private readonly Config $config,
    ) {
    }

    public function process(ServerRequestInterface $request, RequestHandler $handler): ResponseInterface
    {
        $uri = $request->getUri();
        $path = $uri->getPath();
        if ($path !== '/' && str_ends_with($path, '/')) {
            $path = rtrim($path, '/');

            $uri = $uri->withPath($path);

            if ($request->getMethod() === 'GET') {
                $response = new Response();
                return $response
                    ->withHeader('Location', (string) $uri)
                    ->withStatus(301);
            }
        }
        $this->twig->getEnvironment()->addGlobal('request',$request);
        $this->twig->getEnvironment()->addGlobal('url',[
            'file_upload' => $this->config->get('url.file_upload')
        ]);
        $this->twig->getEnvironment()->addGlobal('fileParam','/?code=');
        return $handler->handle($request);

    }

}