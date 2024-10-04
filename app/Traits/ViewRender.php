<?php

declare(strict_types=1);

namespace App\Traits;

use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

trait ViewRender
{
    protected function render(Twig $twig,Response $response, string $templatePath,array $args = []): Response
    {
        try {
            return $twig->render($response, $templatePath,$args);
        } catch (LoaderError|RuntimeError|SyntaxError $e) {
            error_log($e->getMessage());
            $response->getBody()->write('Template Render Error!!');
        }
        return $response;
    }
}