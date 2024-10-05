<?php
/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Enum\Board\BoardType;
use App\Services\MemberBoardService;
use Doctrine\ORM\Exception\NotSupported;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class Menu8Controller extends Controller
{
    public function __construct(
        private readonly Twig $twig,
        private readonly MemberBoardService  $memberBoardService,
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


    /**
     * @throws NotSupported
     */
    public function notice(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,'menu8/member-notice.twig',[
            'lists' => $this->memberBoardService->list($request, BoardType::Notice),
        ]);
    }

    public function noticeDetail(Request $request, Response $response, array $args = []): Response
    {
        $id = isset($args['id'])? (int)$args['id'] : 0;
        return $this->render($this->twig, $response, 'menu8/member-notice-detail.twig',[
            'data' => $this->memberBoardService->getById($id),
        ]);
    }


}