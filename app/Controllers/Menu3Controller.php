<?php
/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Interfaces\AuthInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class Menu3Controller extends Controller
{
    public function __construct(
        private readonly Twig $twig,
    ) {
    }

    public function complex(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu3/complex.twig');
    }


    public function units(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu3/premium10.twig');
    }

    public function community(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu3/community.twig');
    }

    public function garden(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu3/garden.twig');
    }

    public function system(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu3/system.twig');
    }

}