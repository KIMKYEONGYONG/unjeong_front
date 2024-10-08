<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\EntityManager\DefaultEntityManager;
use App\Core\EntityMapper;
use App\Entity\MemberBoard;
use App\Enum\Board\BoardStatus;
use App\Enum\Board\BoardType;
use App\Helper\PaginatorHelper;
use App\Helper\QueryConditionHelper;
use App\Helper\UploadFileHelper;
use App\Traits\EntityServiceTrait;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Psr\Http\Message\ServerRequestInterface as Request;

class MemberBoardService
{
    use EntityServiceTrait;

    public function __construct(
        private readonly DefaultEntityManager $entityManager,
        private readonly EntityMapper $entityMapper,
        private readonly PaginatorHelper $paginator,
        private readonly QueryConditionHelper $conditionHelper,
        private readonly UploadFileHelper        $fileHelper,
    ){}

    /**
     * @throws NotSupported
     */
    public function list(Request $request, BoardType $boardType, string $platform = 'master') : PaginatorHelper
    {
        $query = $this->entityManager
            ->getRepository(MemberBoard::class)
            ->createQueryBuilder('B')
            ->where('B.boardType = :boardType')
            ->setParameter('boardType', $boardType)
            ->orderBy('B.createdAt','desc');
        if($platform !== 'master'){
            $query
                ->andWhere('B.status = :status')
                ->setParameter('status', BoardStatus::Active)
                ;

        }
        $query = $this->conditionHelper->dateCondition($query, 'startDate', 'endDate','B.createdAt',$request);
        $query = $this->conditionHelper->keywordCondition($query,['B.subject'],$request);
        return $this->paginator->paginate($query,$request);
    }

    public function getById(int $id): ?MemberBoard
    {
        try{
            return $this->entityManager->find(MemberBoard::class, $id);
        }catch (ORMException ){}
        return null;
    }


}