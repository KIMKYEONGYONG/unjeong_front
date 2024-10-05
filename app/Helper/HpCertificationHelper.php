<?php

declare(strict_types=1);

namespace App\Helper;

use App\Application\Mode\CertAuthMode;
use App\Entity\HpAuthMember;
use App\Entity\User;
use App\Exception\ValidationException;
use App\Provider\External\MoonLetterProvider;
use App\Repository\UserRepository;
use App\Services\HpCertificationService;
use App\Traits\MemberSecessionCheckTrait;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Valitron\Validator;

class HpCertificationHelper
{

    public function __construct(
        private readonly MoonLetterProvider $sms,
        private readonly HpCertificationService $service,
        private readonly EntityManager $entityManager,
    ) {
    }

    /**
     * @throws Exception
     * @throws GuzzleException
     */
    public function certificationNumberSend(array $data): string
    {
        $phone = $data['phone'];
        $authNum = sprintf('%06d',random_int(000000,999999));
        $this->service->phoneForAuthNoRemoveAll($phone);
        $this->service->authNoInsert((new HpAuthMember())->setPhone($phone)->setAuthNo($authNum));
        $sendMessage = "인증번호 [$authNum]을 입력해주세요.";
        $this->sms->makeMessage($sendMessage,$phone, $authNum);
        return $authNum;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function certificationNumberCheck(array $data): ?array
    {
        $v = new Validator($data);
        $v->rule('required',['phone','authNo']);
        $v->rule('cellphone','phone')->message('휴대폰 번호를 확인 하시기 바랍니다');
        $v->labels(
            [
                'phone' => '휴대폰번호',
                'authNo' => '인증번호'
            ]
        );
        if (!$v->validate()) {
            throw new ValidationException($v->errors());
        }
        /** @var HpAuthMember $hpAuthMember */
        $hpAuthMember = $this->service->getAuthNo($data['phone'],$data['authNo']);
        if ($hpAuthMember !== null) {
            if ($hpAuthMember->isExpired()) {
                $this->service->phoneForAuthNoRemoveAll($data['phone']);
                throw new ValidationException('인증번호가 만료 되었습니다<br>다시 인증요청을 하시기 바랍니다');
            }
            if ($hpAuthMember->isCertComplete()) {
                throw new ValidationException('인증이 완료 되었습니다');
            }
        } else {
            throw new ValidationException('인증 번호가 일치 하지 않습니다');
        }

        $this->service->authNoVerificationcompleted($hpAuthMember);

        return $data;
    }

    /**
     * @throws GuzzleException
     * @throws Exception
     */
    public function requestAuthNoValidator(array $data ,CertAuthMode $mode = CertAuthMode::CERT_AUTHNO_REGISTER): void
    {
        $blockHps = [
            '3ccd4027acb1b6cf389ca3141656b598dc1adb485f2fe93d94d27e3c754a334f',
            '4ae22d32489a0ad51fc2f2accc8ed3db3ce4666b5ebae416cbd5882320ae7326',
            '83c8a7b2dd53445d0ef61e42457ac6c2de42243b8123edf2a671882ac90adc29',
            '0a844af90fff3d801c704522a1350423207b9aa508adde631b0675d02ddfc61d'  // 2023-10-30 추가 (01033634600)
        ];

        if (!empty($data['phone'])) {
            $phoneNumber = str_replace('-', '', $data['phone']);
            if (in_array(hash('sha256', $phoneNumber), $blockHps, true)) {
                throw new ValidationException('해당 전화 번호는 가치워크 운영정책에 따라 더 이상 회원 가입을 하실수 없습니다');
            }
        }


        $v = new Validator($data);
        if ($mode === CertAuthMode::CERT_AUTHNO_FIND_PWD) {
            $v->rule('required','userId')->message('아이디를 입력하세요');
        }
        $v->rule('required','phone')->message('휴대폰 번호를 정확히 입력하세요');
        $v->rule('cellphone','phone')->message('휴대폰 번호를 확인 하시기 바랍니다');
        $v->rule('required','type')->message('인증을 받을 방법을 선택해주세요.');

        /** @var UserRepository $userRepo */
        $userRepo  = $this->entityManager->getRepository(User::class);
        if ($mode !== CertAuthMode::CERT_AUTHNO_CHANGE_HP) {
            $phone = str_replace('-','',$data['phone']);
            $memberSecessionInfo = $userRepo->secessionInfo($phone);
            if($memberSecessionInfo !== null){
                $createdAt = $memberSecessionInfo->getCreatedAt();
                if(!$this->dateCheck($createdAt->format('Y-m-d'))){
                    $message = match ($mode){
                        CertAuthMode::CERT_AUTHNO_SIMPLE_LOGIN => '회원님께서는 탈퇴한 회원이십니다.<br><br>재가입을 원하시는 경우 고객센터로 문의 하시기 바랍니다.',
                        default => "탈퇴된 계정입니다.<br><br>해당 계정은 [".$createdAt->format('Y-m-d H:i:s')."]에 탈퇴 된 계정입니다.<br><br>*탈퇴 후 3개월간 재가입이 불가능합니다."
                    };
                    throw new ValidationException($message);
                }
            }
            if(($mode === CertAuthMode::CERT_AUTHNO_SIMPLE_LOGIN) && $userRepo->isExistenceAndStatusPhone($phone)) {
                throw new ValidationException('탈퇴 회원입니다. 로그인을 진행할 수 없습니다.');
            }
        }

        // 비밀번호찾기의 경우 아이디 + 휴대폰 번호로
        if ($mode === CertAuthMode::CERT_AUTHNO_FIND_PWD ) {
            $isExistence = $userRepo->findOneBy([
                'phone' => str_replace('-','',$data['phone']),
                'userId' => $data['userId']
            ]);
        } else if(in_array($mode, [CertAuthMode::CERT_AUTHNO_REGISTER, CertAuthMode::CERT_AUTHNO_CHANGE_HP], true)) {
            $isExistence = $userRepo->isExistencePhone($data['phone']);
        } else {
            $isExistence = $userRepo->isExistencePhone($data['phone']);
        }
        $phoneNumberCheck = match ($mode) {
            CertAuthMode::CERT_AUTHNO_REGISTER, CertAuthMode::CERT_AUTHNO_CHANGE_HP => ! $isExistence,
            CertAuthMode::CERT_AUTHNO_FIND_ID,CertAuthMode::CERT_AUTHNO_FIND_PWD,CertAuthMode::CERT_AUTHNO_SIMPLE_LOGIN  => $isExistence

        };
        $message = match ($mode) {
            CertAuthMode::CERT_AUTHNO_REGISTER, CertAuthMode::CERT_AUTHNO_CHANGE_HP => '이미 가입된 휴대폰 번호입니다.',
            CertAuthMode::CERT_AUTHNO_FIND_ID,CertAuthMode::CERT_AUTHNO_FIND_PWD,CertAuthMode::CERT_AUTHNO_SIMPLE_LOGIN => '일치하는 회원정보가 없습니다'
        };

        $v->rule(
            fn($field, $value, $params, $fields) => $phoneNumberCheck,
            'phone'
        )->message($message);

        if (!$v->validate()) {
            throw new ValidationException($v->errors());
        }

        if(in_array($mode, [CertAuthMode::CERT_AUTHNO_REGISTER, CertAuthMode::CERT_AUTHNO_CHANGE_HP], true)){
            $phone = str_replace('-','',$data['phone']);
            $isExist = $userRepo->isExistenceId($phone);
            if($isExist){
                throw new ValidationException('해당 휴대폰번호는 다른 회원이 사용하고 있습니다.<br>고객센터로 문의 부탁드립니다.');
            }
        }

        $this->certificationNumberSend( [
            'phone' => $data['phone'],
            'type' => $data['type'],
        ]);
    }
}