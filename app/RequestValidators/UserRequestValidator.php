<?php

declare(strict_types=1);

namespace App\RequestValidators;


use App\Core\EntityManager\DefaultEntityManager;
use App\Core\JsonFormatter;
use App\Entity\HpAuthMember;
use App\Entity\Member;
use App\Enum\ActionMode;
use App\Exception\ValidationException;
use App\Interfaces\RequestValidatorInterface;
use App\Interfaces\SessionInterface;
use App\Repository\UserRepository;
use App\Services\HpCertificationService;
use App\Traits\EntityServiceTrait;
use Doctrine\ORM\Exception\NotSupported;
use Valitron\Validator;

class UserRequestValidator implements RequestValidatorInterface
{
    public function __construct(
        private readonly DefaultEntityManager $entityManager,
        private readonly SessionInterface $session,
        private readonly HpCertificationService $service,
    ) {
    }

    /**
     * @throws NotSupported
     */
    public function validate(array $data, ActionMode $mode = ActionMode::Reg, array $files = []): array
    {

        /** @var HpAuthMember $hpAuthMember */
        $hpAuthMember = $this->service->getAuthNo($data['phone'],$data['authenticationNum']);
        /** @var UserRepository $userRepo */
        $userRepo  = $this->entityManager->getRepository(Member::class);
        $data['agree'] = $this->session->get('agree');
        $v = new Validator($data);

        $v->addInstanceRule('idCheck',
            fn($field, $value, array $params, array $fields) =>! $userRepo->isExistenceId($value),
            '이미 가입된 {field} 입니다'
        );

        $v->addInstanceRule('phoneCheck',
            fn($field, $value, array $params, array $fields) =>! $userRepo->isExistencePhone($value),
            '이미 가입된 {field} 입니다'
        );


        $v->addInstanceRule('authNoConfirm',
            fn($field, $value, array $params, array $fields) => $hpAuthMember?->getIsAuth() === 'T',
            '{field} 인증이 완료 되지 않았습니다');


        $rules = [
            'userId' => ['required','idCheck'],
            'password' => ['required'],
            'phone' => ['required','cellphone','phoneCheck','authNoConfirm'],
            'name' => ['required',['lengthMax', 20]],
            'birthDay' => ['required',['length', 8]],
            'addr' => ['required',['lengthMax', 255]],
        ];
        $v->mapFieldsRules($rules);



        $v->labels(
            [
                'userId' => '아이디',
                'password' => '비밀번호',
                'name' => '이름',
                'birthDay' => '생년월일',
                'phone' => '휴대폰 번호',
                'addr' => '주소',
                'authenticationNum' => '인증번호',
            ]
        );
        if (!$v->validate()) {
            throw new ValidationException($v->errors());
        }

        $data['phone'] = str_replace('-','',$data['phone']);

        return $data;
    }
}