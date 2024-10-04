<?php

declare(strict_types=1);

namespace App\Traits;

use App\Core\EntityManager\DefaultEntityManager;
use App\Interfaces\EntityInterface;
use Doctrine\ORM\Exception\ORMException;

trait EntityServiceTrait
{
    public function __construct(
        private readonly DefaultEntityManager $entityManager,
    ){}

    /**
     * @throws ORMException
     */
    public function persistFlush(EntityInterface $entity): void
    {
        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    /**
     * @throws ORMException
     */
    public function removeFlush(EntityInterface $entity): void
    {
        $this->entityManager->remove($entity);
        $this->entityManager->flush();
    }
}