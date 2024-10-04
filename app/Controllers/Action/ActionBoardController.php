<?php
/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace App\Controllers\Action;


use App\Core\ResponseFormatter;
use App\Enum\ActionMode;
use App\Enum\Board\BoardType;
use App\Exception\ValidationException;
use App\Interfaces\RequestValidatorFactoryInterface;
use App\RequestValidators\BoardRequestValidator;
use App\Services\BoardService;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use ReflectionException;

class ActionBoardController
{
    public function __construct(
        private readonly RequestValidatorFactoryInterface $requestValidatorFactory,
        private readonly ResponseFormatter $responseFormatter,
        private readonly BoardService $service
    ) {
    }

    /**
     * @throws ReflectionException| ORMException
     */
    public function register(Request $request, Response $response, array $args = []): Response
    {
        $user = $request->getAttribute('user');
        $type = $args['boardType'];
        $data = $this->requestValidatorFactory->make(BoardRequestValidator::class)->validate(
            [ 'adminMember' => $user,'boardType' => $type ]+$request->getParsedBody(), files: $request->getUploadedFiles()
        );
        $this->service->register($data);
        return $response;
    }

    /**
     * @throws ReflectionException| ORMException
     */
    public function update(Request $request, Response $response, array $args = []): Response
    {
        $user = $request->getAttribute('user');
        $id = isset($args['id'])? (int)$args['id'] : 0;
        if(!$board = $this->service->getById($id)){
            throw new ValidationException('Bad Request');
        }
        $data = $this->requestValidatorFactory->make(BoardRequestValidator::class)->validate(
            [ 'board' =>$board, 'adminMember' => $user, 'bdName' => $user->getName(), 'boardType' => $board->getBoardType()->value ]+$request->getParsedBody(), ActionMode::Edit,  $request->getUploadedFiles()
        );
        $this->service->update($board,$data);
        return $response;
    }

    /**
     * @throws ORMException
     */
    public function delete(Request $request, Response $response, array $args = []): Response
    {
        $id = isset($args['id'])? (int)$args['id'] : 0;
        if(!$board = $this->service->getById($id)){
            throw new ValidationException('Bad Request');
        }
        $this->service->delete($board);
        return $response;
    }

    /**
     * @throws NotSupported
     */
    public function list(Request $request, Response $response, array $args) : Response
    {
        $code = $args['code'];
        $boardType = BoardType::tryFrom($code);
        if($boardType === null){
            return $this->responseFormatter->asJson($response, []);
        }
        return $this->responseFormatter->asJson($response, $this->service->listJson($request, $boardType));
    }


    public function detail(Request $request, Response $response, array $args) : Response
    {
        $id = isset($args['id'])? (int)$args['id'] : 0;
        if(!$board = $this->service->getById($id)){
            return $this->responseFormatter->asJson($response, []);
        }
        return $this->responseFormatter->asJson($response, $this->service->detailJson($board));
    }

}