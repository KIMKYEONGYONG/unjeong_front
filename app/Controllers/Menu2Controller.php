<?php
/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Interfaces\AuthInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class Menu2Controller extends Controller
{
    public function __construct(
        private readonly Twig $twig,
    ) {
    }

    public function intro(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu2/intro.twig');
    }


    public function premium10(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu2/premium10.twig');
    }


}