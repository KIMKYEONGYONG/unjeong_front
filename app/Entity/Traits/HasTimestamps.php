<?php

declare(strict_types=1);

namespace App\Entity\Traits;

use DateTime;
use Doctrine\DBAL\Types\Types;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Doctrine\Persistence\Event\LifecycleEventArgs;

trait HasTimestamps
{
    #[Column(name: 'created_at',type: Types::DATETIME_MUTABLE)]
    private DateTime $createdAt;

    #[Column(name: 'updated_at',type: Types::DATETIME_MUTABLE)]
    private DateTime $updatedAt;

    /** @noinspection PhpUnusedParameterInspection
     * @noinspection PhpUnused
     */
    #[PrePersist, PreUpdate]
    public function updateTimestamps(LifecycleEventArgs $args): void
    {
        if (! isset($this->createdAt)) {
            $this->createdAt = new DateTime();
        }

        $this->updatedAt = new DateTime();
    }

    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

}