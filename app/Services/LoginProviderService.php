<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\EntityManager\DefaultEntityManager;
use App\Entity\Member;
use App\Interfaces\AuthUserInterface;
use App\Interfaces\LoginProviderServiceInterface;
use Doctrine\ORM\Exception\NotSupported;

class LoginProviderService implements LoginProviderServiceInterface
{
    public function __construct(
        private readonly DefaultEntityManager $entityManager
    ){}


    /**
     * @throws NotSupported
     */
    public function getFindOneBy(array $criteria): ?AuthUserInterface
    {
        return $this->entityManager->getRepository(Member::class)->findOneBy($criteria);
    }

    /**
     * @throws NotSupported
     */
    public function getByCredentials(array $credentials): ?AuthUserInterface
    {
        return $this->entityManager
            ->getRepository(Member::class)
            ->findOneBy([
                'userId' => $credentials['userId']
            ]);
    }
}