<?php /** @noinspection ForgottenDebugOutputInspection */
/** @noinspection PhpFieldAssignmentTypeMismatchInspection */

declare(strict_types=1);

namespace App\Services;


use App\Core\EntityMapper;
use App\Entity\Member;
use App\Interfaces\AuthInterface;
use App\Repository\UserRepository;
use App\Traits\EntityServiceTrait;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use ReflectionException;

class AccountService
{
    use EntityServiceTrait;

    private UserRepository $userRepository;
    public function __construct(
        private readonly EntityMapper $entityMapper,
        private readonly AuthInterface $auth,

    ) {
        $this->userRepository = $this->entityManager->getRepository(Member::class);
    }

    /**
     * @throws ReflectionException | OptimisticLockException | ORMException
     */
    public function register(array $data): Member
    {
        /** @var Member $user */
        $user = $this->entityMapper->mapper(Member::class,$data,['password'], ['source']);
        $this->userRepository->register($user,$data);

        $this->auth->attemptLogin($data);
        return $user;
    }

    public function passwordReset(Member $user, string $password): void
    {
        $this->userRepository->passwordUpdate($user,password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]));
    }
}