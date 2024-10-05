<?php
/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class Menu8Controller extends Controller
{
    public function __construct(
        private readonly Twig $twig,

    ) {
    }



    public function signup_terms(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu8/signup-terms.twig');
    }

    public function signup(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu8/signup.twig');
    }


    public function signup_complete(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu8/signup-complete.twig');
    }

}