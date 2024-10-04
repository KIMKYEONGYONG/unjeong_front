<?php
/** @noinspection PhpUnused */
/** @noinspection PhpPropertyOnlyWrittenInspection */


declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\HasTimestamps;
use App\Enum\Client\ClientAge;
use App\Enum\Gender;
use App\Helper\ReadFileHelper;
use App\Interfaces\EntityInterface;
use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\Table;

#[Entity, Table('client')]
#[HasLifecycleCallbacks]
class Client implements EntityInterface
{
    use HasTimestamps;

    #[Id, Column(options: ['unsigned' => true]), GeneratedValue]
    private int $id;

    #[Column]
    private string $name;

    #[Column]
    private string $phone;

    #[Column(enumType: ClientAge::class)]
    private ClientAge $age;

    #[Column(enumType: Gender::class)]
    private Gender $gender;

    #[Column]
    private string $addr;

    public function getId(): int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getAge(): ClientAge
    {
        return $this->age;
    }

    public function getGender(): Gender
    {
        return $this->gender;
    }

    public function getAddr(): string
    {
        return $this->addr;
    }


}