<?php
/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Enum\Board\BoardStatus;
use App\Enum\Board\BoardType;
use App\Services\BoardService;
use Doctrine\ORM\Exception\NotSupported;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class BoardController extends Controller
{
    public function __construct(
        private readonly Twig $twig,
        private readonly BoardService $service
    ) {
    }

    /**
     * @throws NotSupported
     */
    public function list(Request $request, Response $response, array $args = []): Response
    {
        $code = $args['code'];
        $boardType = BoardType::tryFrom($code);
        if($boardType === null){
            return $response->withHeader('Location', '/')->withStatus(301);
        }
        return $this->render($this->twig,$response,"board/list.twig",[
            'lists' => $this->service->list($request, $boardType),
            'boardType' => $boardType ,
        ]);
    }

    public function register(Request $request, Response $response, array $args = []): Response
    {
        $user  = $request->getAttribute('user');
        $code = $args['code'];
        $id = isset($args['id'])? (int)$args['id'] : 0;
        $boardType = BoardType::tryFrom($code);
        if($boardType === null){
            return $response->withHeader('Location', '/')->withStatus(301);
        }
        return $this->render($this->twig,$response,"board/register.twig",[
            'user' => $user,
            'data' => $this->service->getById($id),
            'boardType' => $boardType ,
            'statuses' => BoardStatus::cases(),
        ]);
    }
}