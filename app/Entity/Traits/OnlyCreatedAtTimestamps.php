<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use DateTime;
use Doctrine\DBAL\Types\Types;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\Persistence\Event\LifecycleEventArgs;

trait OnlyCreatedAtTimestamps
{
    #[Column(name: 'created_at',type: Types::DATETIME_MUTABLE)]
    private DateTime $createdAt;


    /** @noinspection PhpUnusedParameterInspection
     * @noinspection PhpUnused
     */
    #[PrePersist]
    public function updateTimestamps(LifecycleEventArgs $args): void
    {
        if (! isset($this->createdAt)) {
            $this->createdAt = new DateTime();
        }
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

}