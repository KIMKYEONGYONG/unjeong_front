<?php

declare(strict_types=1);

namespace App\Controllers\Action;


use App\Core\ResponseFormatter;
use App\Entity\Member;
use App\Enum\CertAuthMode;
use App\Helper\HpCertificationHelper;
use App\Services\HpCertificationService;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\ORMException;
use Doctrine\ORM\OptimisticLockException;
use GuzzleHttp\Exception\GuzzleException;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ActionCertNoController
{
    public function __construct(
        private readonly HpCertificationHelper $helper,
        private readonly HpCertificationService $hpCertificationService,
        private readonly ResponseFormatter $responseFormatter,
        private readonly EntityManager $entityManager,
    ) {
    }

    /**
     * @throws GuzzleException
     */
    public function sendCertNumber(Request $request ,Response $response,array $args = []): Response
    {
        $this->helper->requestAuthNoValidator(
            $request->getParsedBody(),CertAuthMode::tryFrom((int)$args['mode'])
        );
        return $response;
    }

    /**
     * @throws OptimisticLockException
     * @throws ORMException
     */
    public function certNoCheck(Request $request ,Response $response,array $args): Response
    {
        $data = $this->helper->certificationNumberCheck(
            $request->getParsedBody()
        );

        if (CertAuthMode::tryFrom((int)$args['mode']) === CertAuthMode::CERT_AUTHNO_FIND_ID) {
            $userRepo = $this->entityManager->getRepository(Member::class);
            $user = $userRepo->findOneBy(['phone' => str_replace('-','',$data['phone'])]);

            if ($user === null) {
                return $response->withStatus(422);
            }
            $this->hpCertificationService->phoneForAuthNoRemoveAll(str_replace('-','',$data['phone']));

            $authCode = "";

            return $this->responseFormatter->asJson($response,[
                'userId' => $user->getUserId(),
                'code' => $authCode
            ]);
        }

        if (CertAuthMode::tryFrom((int)$args['mode']) === CertAuthMode::CERT_AUTHNO_FIND_PWD) {
            $userRepo = $this->entityManager->getRepository(Member::class);
            $user = $userRepo->findOneBy(['phone' => str_replace('-','',$data['phone'])]);

            if ($user === null) {
                return $response->withStatus(422);
            }
            $authCode = "";
            $this->hpCertificationService->phoneForAuthNoRemoveAll($data['phone']);
            return $this->responseFormatter->asJson($response,
                ['authCode' => $authCode]
            );
        }

        return $response;
    }
}