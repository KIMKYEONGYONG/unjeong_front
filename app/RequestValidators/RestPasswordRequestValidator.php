<?php

declare(strict_types=1);

namespace App\RequestValidators;

use App\Application\Cipher\RsaCipher;
use App\Application\Formatter;
use App\Application\Utility;
use App\Core\EntityManager\DefaultEntityManager;
use App\Entity\Member;
use App\Entity\User;
use App\Enum\ActionMode;
use App\Exception\ValidationException;
use App\Interfaces\RequestValidatorInterface;
use Doctrine\ORM\EntityManager;
use Valitron\Validator;

class RestPasswordRequestValidator implements RequestValidatorInterface
{
    public function __construct(
        private readonly DefaultEntityManager $entityManager,
    ){
    }

    public function validate(array $data, ActionMode $mode = ActionMode::Reg, array $files = []): array
    {
        $userRepo = $this->entityManager->getRepository(Member::class);
        $user = $userRepo->findOneBy(['userId' => $data['userId']]);

        $v = new Validator($data);


        $v->rule('required',['pwd','repwd']);
        $v->rule('lengthMin','pwd',6);

        $v->rule('equals', 'repwd', 'pwd')->message('신규 비밀번호가 일치 하지 않습니다');

        $v->labels(
            [
                'pwd' => '신규 비밀번호',
                'repwd' => '신규 비밀번호 확인'
            ]
        );
        if (!$v->validate()) {
            throw new ValidationException($v->errors());
        }
        $data['user'] = $user;
        return $data;
    }
}