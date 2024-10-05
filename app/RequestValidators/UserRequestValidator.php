<?php

declare(strict_types=1);

namespace App\RequestValidators;


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
    use EntityServiceTrait;

    public function __construct(
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

        $v->addInstanceRule('phoneCheck',
            fn($field, $value, array $params, array $fields) =>! $userRepo->isExistencePhone($value),
            '이미 가입된 {field} 입니다'
        );
        $v->addInstanceRule('byPhoneIdCheck',
            fn($field, $value, array $params, array $fields) =>! $userRepo->isExistenceId(str_replace('-','',$value)),
            '해당 {field}는 다른 회원이 사용하고 있습니다.<br>고객센터로 문의 부탁드립니다.'
        );

        $v->addInstanceRule('authNoConfirm',
            fn($field, $value, array $params, array $fields) => $hpAuthMember?->getIsAuth() === 'T',
            '{field} 인증이 완료 되지 않았습니다');


        $rules = [
            'phone' => ['required','cellphone','phoneCheck','authNoConfirm','byPhoneIdCheck'],
        ];
        $v->mapFieldsRules($rules);



        $v->labels(
            [

                'phone' => '휴대폰 번호',
                'authenticationNum' => '인증번호'
            ]
        );
        if (!$v->validate()) {
            throw new ValidationException($v->errors());
        }

        $data['phone'] = str_replace('-','',$data['phone']);

        return $data;
    }
}