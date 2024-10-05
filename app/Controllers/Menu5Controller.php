<?php
/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Interfaces\AuthInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class Menu5Controller extends Controller
{
    public function __construct(
        private readonly Twig $twig,
    ) {
    }

    public function news(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu5/news.twig');
    }

    public function videos(Request $request, Response $response): Response
    {
        return $response->withHeader('Location', '/wip')->withStatus(302);
        //return $this->render($this->twig,$response,'menu5/videos.twig');
    }


    public function vr(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu5/vr.twig');
    }


}