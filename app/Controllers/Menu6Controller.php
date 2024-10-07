<?php
/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Enum\Board\BoardType;
use App\Interfaces\AuthInterface;
use App\Services\BoardService;
use Doctrine\ORM\Exception\NotSupported;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Views\Twig;

class Menu6Controller extends Controller
{
    public function __construct(
        private readonly Twig $twig,
        private readonly BoardService $boardService,
    ) {
    }

    /**
     * @throws NotSupported
     */
    public function notice(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu6/notice.twig',[
            'lists' => $this->boardService->list($request, BoardType::Notice, 'front'),
        ]);
    }

    public function noticeDetail(Request $request, Response $response, array $args = []): Response
    {
        $id = isset($args['id'])? (int)$args['id'] : 0;
        return $this->render($this->twig, $response, 'menu6/notice-details.twig',[
            'data' => $this->boardService->getById($id),
        ]);
    }


    public function reg(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu6/reg.twig');
    }


}