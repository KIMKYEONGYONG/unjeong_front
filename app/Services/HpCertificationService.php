<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\HpAuthMember;
use App\Interfaces\EntityInterface;
use App\Traits\EntityServiceTrait;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;

class HpCertificationService
{
    use EntityServiceTrait;


    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function authNoInsert(EntityInterface $entity): void
    {
        $this->persistFlush($entity);
    }


    /**
     * @throws NotSupported
     */
    public function getAuthNo(string $phone , string $authNo): ?HpAuthMember
    {
        return $this->entityManager->getRepository(HpAuthMember::class)->findOneBy(
            [
                'phone' => $phone,
                'authNo' => $authNo
            ]
        );
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function authNoVerificationcompleted(EntityInterface $entity) : void
    {
        /** @var HpAuthMember $entity */
        $entity->setIsAuth('T');
        $this->persistFlush($entity);
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function phoneForAuthNoRemoveAll(string $phone): void
    {
        $builder = $this->entityManager->createQueryBuilder()
            ->delete(HpAuthMember::class,'A')
            ->where('A.phone = :phone')
            ->setParameter(':phone',$phone);
        $builder->getQuery()->execute();

        $this->entityManager->flush();
    }
}