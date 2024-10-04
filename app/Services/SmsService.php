<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\EntityManager\DefaultEntityManager;
use App\Core\EntityMapper;
use App\Entity\Sms;
use App\Helper\PaginatorHelper;
use App\Helper\QueryConditionHelper;
use App\Traits\EntityServiceTrait;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Psr\Http\Message\ServerRequestInterface as Request;
use ReflectionException;

class SmsService
{

    use EntityServiceTrait;

    public function __construct(
        private readonly EntityMapper $entityMapper,
        private readonly DefaultEntityManager $entityManager,
        private readonly PaginatorHelper $paginator,
        private readonly QueryConditionHelper $conditionHelper,
    ){}

    /**
     * @throws NotSupported
     */
    public function list(Request $request) : PaginatorHelper
    {
        $query = $this->entityManager
            ->getRepository(Sms::class)
            ->createQueryBuilder('S')
            ->orderBy('S.createdAt','desc');
        $query = $this->conditionHelper->keywordCondition($query,['S.name','S.phone'],$request);
        return $this->paginator->paginate($query,$request);
    }



    /**
     * @throws ReflectionException| ORMException
     */
    public function register(array $data): void
    {
        $sms = $this->entityMapper->mapper(Sms::class,$data);
        $this->persistFlush($sms);
    }

    public function getById(int $id): ?Sms
    {
        try{
            return $this->entityManager->find(Sms::class, $id);
        }catch (ORMException ){}
        return null;
    }

    /**
     * @throws NotSupported
     */
    public function getByData(array $data) : array
    {
        return $this->entityManager->getRepository(Sms::class)->findBy($data);
    }


    /**
     * @throws ORMException
     */
    public function delete(Sms $smsHistory): void
    {
        $this->removeFlush($smsHistory);
    }

}