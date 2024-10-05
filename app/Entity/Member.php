<?php
/** @noinspection PhpPropertyOnlyWrittenInspection */

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\OnlyCreatedAtTimestamps;
use App\Interfaces\AuthUserInterface;
use App\Interfaces\EntityInterface;
use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity(repositoryClass: UserRepository::class)]
#[Table('member')]
class Member implements AuthUserInterface, EntityInterface
{
    use OnlyCreatedAtTimestamps;

    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column(name:"user_id")]
    private string $userId;

    #[Column]
    private string $name;

    #[Column]
    private string $password;

    #[Column(name: 'birthday',type: Types::STRING)]
    private string $birthDay;

    #[Column(type: Types::STRING,unique: true)]
    private string $phone;

    #[Column(name: 'addr',type: Types::STRING)]
    private string $addr;

    public function getId(): int
    {
        return $this->id;
    }

    public function getUserId(): string
    {
        return $this->userId;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getBirthDay(): string
    {
        return $this->birthDay;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getAddr(): string
    {
        return $this->addr;
    }


}