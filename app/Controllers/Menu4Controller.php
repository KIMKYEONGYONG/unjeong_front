<?php
/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Interfaces\AuthInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class Menu4Controller extends Controller
{
    public function __construct(
        private readonly Twig $twig,
    ) {
    }

    public function floorplanA(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu4/floorplanA.twig');
    }

    public function floorplanB(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu4/floorplanB.twig');
    }

    public function floorplanB1(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu4/floorplanB1.twig');
    }

    public function interior(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu4/interior.twig');
    }

    public function interior_bed(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu4/interior-bed.twig');
    }

    public function interior_dining(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu4/interior-dining.twig');
    }

    public function interior_bath(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu4/interior-bath.twig');
    }

    public function interior_etc(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu4/interior-etc.twig');
    }
}