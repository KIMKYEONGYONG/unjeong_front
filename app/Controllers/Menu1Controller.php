<?php
/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Interfaces\AuthInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class Menu1Controller extends Controller
{
    public function __construct(
        private readonly Twig $twig,
    ) {
    }

    public function business(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu1/business.twig');
    }


    public function architechture(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu1/architechture.twig');
    }

    public function location(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu1/location.twig');
    }


}