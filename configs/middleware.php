<?php

declare(strict_types=1);


use App\Core\Config;
use App\Handlers\HttpErrorHandler;
use App\Handlers\ShutdownHandler;
use App\Middleware\CsrfFieldsMiddleware;
use App\Middleware\RequestMiddleware;
use App\Middleware\StartSessionsMiddleware;
use App\Middleware\ValidationExceptionMiddleware;
use DI\Container;
use Slim\App;
use Slim\Factory\ServerRequestCreatorFactory;
use Slim\Middleware\MethodOverrideMiddleware;
use Slim\Views\Twig;
use Slim\Views\TwigMiddleware;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

return static function (App $app) {
    /** @var Container $container */
    $container = $app->getContainer();

    /** @var Config $config */
    $config = $container?->get(Config::class);

    $app->addRoutingMiddleware();
    $app->add(RequestMiddleware::class);
    $app->add(CsrfFieldsMiddleware::class);
    $url = $_SERVER['REQUEST_URI'] ?? '';
    if (!str_contains($url, 'api')) {
        $app->add('csrf');
    }
    $app->add(MethodOverrideMiddleware::class);
    $app->add(ValidationExceptionMiddleware::class);
    $app->add(TwigMiddleware::create($app,$container?->get(Twig::class)));
    $app->add(StartSessionsMiddleware::class);
    $app->addBodyParsingMiddleware();
    $errorMiddleware = $app->addErrorMiddleware(
        (bool) $config->get('display_error_details'),
        (bool) $config->get('log_errors'),
        (bool) $config->get('log_error_details')
    );

    $callableResolver = $app->getCallableResolver();
    $responseFactory = $app->getResponseFactory();
    $errorHandler = new HttpErrorHandler($callableResolver, $responseFactory);
    $errorMiddleware->setDefaultErrorHandler($errorHandler);

    $serverRequestCreator = ServerRequestCreatorFactory::create();
    $request = $serverRequestCreator->createServerRequestFromGlobals();

    $shutdownHandler = new ShutdownHandler($request, $errorHandler, (bool) $config->get('display_error_details'));
    register_shutdown_function($shutdownHandler);


    $httpOrigin = $request->getServerParams()['HTTP_ORIGIN'] ?? null;
    if(!empty($httpOrigin)){
        $app->add(function (Request $request, RequestHandlerInterface $handler) use ($httpOrigin): Response {
            $requestHeaders = $request->getHeaderLine('Access-Control-Request-Headers');
            $response = $handler->handle($request);
            $allowed_origin =[
                'https://xn--oi2bp7bw9b2xhr1dk7hskekqrk6j.com',
                'http://localhost:4000',
                'http://xn--oi2bp7bw9b2xhr1dk7hskekqrk6j.com',
            ];
            if (in_array($httpOrigin, $allowed_origin, true)) {
                $response = $response->withHeader("Access-Control-Allow-Origin",$httpOrigin);
            }

            $response = $response->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS');
            $response = $response->withHeader('Access-Control-Allow-Headers', $requestHeaders);
            return $response->withHeader('Access-Control-Allow-Credentials', 'true');
        });
    }


};