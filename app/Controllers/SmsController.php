<?php
/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Services\SmsService;
use Doctrine\ORM\Exception\NotSupported;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class SmsController extends Controller
{
    public function __construct(
        private readonly Twig $twig,
        private readonly SmsService $service
    ) {
    }

    /**
     * @throws NotSupported
     */
    public function list(Request $request, Response $response): Response
    {
        /*
        return $this->render($this->twig,$response,"apply/sms/list.twig",[
            'lists' => $this->service->list($request),
        ]);
        */

        return $response->withHeader('Location', '/apply/client/list')->withStatus(302);
    }
}