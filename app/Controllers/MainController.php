<?php
/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Interfaces\AuthInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class MainController extends Controller
{
    public function __construct(
        private readonly Twig $twig,
        private readonly AuthInterface $auth,
    ) {
    }

    public function loginView(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'auth/login.twig');
    }

    public function logOut(Request $request, Response $response): Response
    {
        $this->auth->logOut();

        return $response->withHeader('Location', '/auth/login')->withStatus(302);
    }

    public function index(Request $request ,Response $response): Response
    {
        return $this->render($this->twig,$response,'index.twig');
    }

    public function wip(Request $request ,Response $response): Response
    {
        return $this->render($this->twig,$response,'404.twig');
    }
}