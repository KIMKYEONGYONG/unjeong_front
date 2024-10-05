<?php
/** @noinspection PhpUnused */

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\HasTimestamps;
use App\Interfaces\EntityInterface;
use Carbon\Carbon;
use DateTime;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table('hp_auth_mumber')]
#[HasLifecycleCallbacks]
class HpAuthMember implements EntityInterface
{
    use HasTimestamps;

    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column(type: Types::STRING)]
    private ?string $phone;

    #[Column(name: 'is_auth',type: Types::STRING,length: 1)]
    private string $isAuth = 'F';

    #[Column(name: 'auth_no', type: Types::STRING, length: 6)]
    private ?string $authNo;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): HpAuthMember
    {
        $this->id = $id;
        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): HpAuthMember
    {
        $this->phone = $phone;
        return $this;
    }

    public function getIsAuth(): string
    {
        return $this->isAuth;
    }

    public function setIsAuth(string $isAuth): HpAuthMember
    {
        $this->isAuth = $isAuth;
        return $this;
    }

    public function getAuthNo(): ?string
    {
        return $this->authNo;
    }

    public function setAuthNo(?string $authNo): HpAuthMember
    {
        $this->authNo = $authNo;
        return $this;
    }


    public function getCreatedAt(): DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): HpAuthMember
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getUpdatedAt(): DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(DateTime $updatedAt): HpAuthMember
    {
        $this->updatedAt = $updatedAt;

        return $this;
    }

    public function isExpired(): bool
    {
        $dt = Carbon::now();
        $minute = $dt->diffInMinutes($this->getCreatedAt());
        return $minute >= 2;
    }

    public function isCertComplete(): bool
    {
        return $this->isAuth === 'T';
    }
}