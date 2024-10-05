<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\EntityManager\DefaultEntityManager;
use App\Core\EntityMapper;
use App\Core\Utility;
use App\Entity\Client;
use App\Helper\PaginatorHelper;
use App\Helper\QueryConditionHelper;
use App\Traits\EntityServiceTrait;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ServerRequestInterface as Request;
use ReflectionException;

class ClientService
{
    use EntityServiceTrait;

    public function __construct(
        private readonly DefaultEntityManager $entityManager,
        private readonly EntityMapper $entityMapper,
        private readonly PaginatorHelper $paginator,
        private readonly QueryConditionHelper $conditionHelper,
        private readonly Utility $utility,
    ){}

    /**
     * @throws NotSupported
     */
    public function list(Request $request) : PaginatorHelper
    {
        $query = $this->entityManager
            ->getRepository(Client::class)
            ->createQueryBuilder('C')
            ->orderBy('C.createdAt','desc');
        $query = $this->conditionHelper->dateCondition($query, 'startDate', 'endDate','C.createdAt',$request);
        $query = $this->conditionHelper->keywordCondition($query,['C.name','C.phone'],$request);
        $query = $this->conditionHelper->eqCondition(
            $query,
            [
                'C.gender' => 'gender',
                'C.age' => 'age',
            ],
            $request
        );
        return $this->paginator->paginate($query,$request);
    }


    /**
     * @throws ReflectionException| ORMException
     */
    public function register(array $data): void
    {
        /** @var Client $client */
        $client = $this->entityMapper->mapper(Client::class,$data);
        $this->persistFlush($client);

    }

}