<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\BioAgeScore;
use App\Entity\FcMember;
use App\Entity\FcmToken;
use App\Entity\MarketingAgreeHistory;
use App\Entity\Member;
use App\Entity\MemberDonationHistory;
use App\Entity\MemberLevel;
use App\Entity\MemberSecession;
use App\Entity\Point\MemberPointEarn;
use App\Entity\Point\MemberPointHistory;
use App\Entity\Point\MemberPointUse;
use App\Entity\User;
use App\Entity\UserRegisterHistory;
use App\Enum\MarketingAgreeType;
use App\Enum\MemberSource;
use App\Enum\MemberStatus;
use App\Enum\Point;
use App\Enum\RegisterType;
use App\Enum\SuspensionStatus;
use App\Interfaces\EntityInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;

class UserRepository extends EntityRepository
{
    public function isExistenceId(string $memberId): bool
    {
        return $this->count(['userId' => $memberId]) !== 0;
    }


    public function isExistencePhone(string $phone): bool
    {
        return $this->count(['phone' => str_replace('-','',$phone)]) !== 0;
    }

    public function register(Member $user, array $data): void
    {
        $this->persistFlush($user);
    }

    public function passwordUpdate(Member $user,string $password): void
    {
        $user->setPassword($password);
        $this->persistFlush($user);
    }

    public function persistFlush(EntityInterface $entity): void
    {
        $this->_em->persist($entity);
        $this->_em->flush();
    }


}