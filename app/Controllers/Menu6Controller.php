<?php
/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Interfaces\AuthInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class Menu6Controller extends Controller
{
    public function __construct(
        private readonly Twig $twig,
    ) {
    }

    public function notice(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu6/notice.twig');
    }

    public function noticeDetail(Request $request, Response $response): Response
    {
        return $this->render($this->twig, $response, 'menu6/notice-details.twig');
    }


    public function reg(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu6/reg.twig');
    }


}