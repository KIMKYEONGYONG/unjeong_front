<?php
/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class Menu7Controller extends Controller
{
    public function __construct(
        private readonly Twig $twig,

    ) {
    }


    public function login(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu7/login.twig');
    }

    public function idFind(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu7/idFind.twig');
    }

    public function idFind2(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu7/idFind2.twig');
    }
}