<?php

declare(strict_types=1);

namespace App\Helper;

use App\Core\EntityManager\DefaultEntityManager;
use App\Entity\HpAuthMember;
use App\Entity\Member;
use App\Enum\CertAuthMode;
use App\Exception\ValidationException;
use App\Provider\External\MoonLetterProvider;
use App\Repository\UserRepository;
use App\Services\HpCertificationService;
use App\Traits\EntityServiceTrait;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Valitron\Validator;

class HpCertificationHelper
{

    public function __construct(
        private readonly DefaultEntityManager $entityManager,
        private readonly MoonLetterProvider $sms,
        private readonly HpCertificationService $service,
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
            /*
            if ($hpAuthMember->isExpired()) {
                $this->service->phoneForAuthNoRemoveAll($data['phone']);
                throw new ValidationException('인증번호가 만료 되었습니다<br>다시 인증요청을 하시기 바랍니다');
            }
            */
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

        $v = new Validator($data);
        if ($mode === CertAuthMode::CERT_AUTHNO_FIND_PWD) {
           // $v->rule('required','userId')->message('아이디를 입력하세요');
        }
        $v->rule('required','phone')->message('휴대폰 번호를 정확히 입력하세요');
        $v->rule('cellphone','phone')->message('휴대폰 번호를 확인 하시기 바랍니다');

        /** @var UserRepository $userRepo */
        $userRepo  = $this->entityManager->getRepository(Member::class);

        // 비밀번호찾기의 경우 아이디 + 휴대폰 번호로
        if ($mode === CertAuthMode::CERT_AUTHNO_FIND_PWD ) {
            $isExistence = $userRepo->findOneBy([
                'phone' => str_replace('-','',$data['phone']),
              //  'userId' => $data['userId']
            ]);
        } else if(in_array($mode, [CertAuthMode::CERT_AUTHNO_REGISTER, CertAuthMode::CERT_AUTHNO_CHANGE_HP], true)) {
            $isExistence = $userRepo->isExistencePhone($data['phone']);
        } else {
            $isExistence = $userRepo->isExistencePhone($data['phone']);
        }
        $phoneNumberCheck = match ($mode) {
            CertAuthMode::CERT_AUTHNO_REGISTER, CertAuthMode::CERT_AUTHNO_CHANGE_HP => ! $isExistence,
            CertAuthMode::CERT_AUTHNO_FIND_ID,CertAuthMode::CERT_AUTHNO_FIND_PWD => $isExistence

        };
        $message = match ($mode) {
            CertAuthMode::CERT_AUTHNO_REGISTER, CertAuthMode::CERT_AUTHNO_CHANGE_HP => '이미 가입된 휴대폰 번호입니다.',
            CertAuthMode::CERT_AUTHNO_FIND_ID,CertAuthMode::CERT_AUTHNO_FIND_PWD => '일치하는 회원정보가 없습니다'
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
        ]);
    }
}