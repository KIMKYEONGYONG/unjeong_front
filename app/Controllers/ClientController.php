<?php
/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Enum\Client\ClientAge;
use App\Enum\Gender;
use App\Services\ClientService;
use Doctrine\ORM\Exception\NotSupported;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class ClientController extends Controller
{
    public function __construct(
        private readonly Twig $twig,
        private readonly ClientService $service
    ) {
    }

    /**
     * @throws NotSupported
     */
    public function list(Request $request, Response $response): Response
    {
        return $this->render($this->twig,$response,"apply/client/list.twig",[
            'lists' => $this->service->list($request),
            'genderCases' => Gender::cases(),
            'ageCases' => ClientAge::cases(),
        ]);
    }
}