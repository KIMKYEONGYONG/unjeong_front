<?php

declare(strict_types=1);

namespace App\Services;

use App\Core\EntityManager\DefaultEntityManager;
use App\Core\EntityMapper;
use App\Entity\SmsHistory;
use App\Provider\External\MoonLetterProvider;
use App\Traits\EntityServiceTrait;
use Doctrine\ORM\Exception\ORMException;
use GuzzleHttp\Exception\GuzzleException;
use ReflectionException;

class SmsHistoryService
{

    use EntityServiceTrait;

    public function __construct(
        private readonly EntityMapper $entityMapper,
        private readonly DefaultEntityManager $entityManager,
        private readonly MoonLetterProvider $moonLetterProvider,
    ){}


    /**
     * @throws ReflectionException| ORMException | GuzzleException
     */
    public function sendMessage(string $sendMsg = '', string $receiverNb = '', string $templateCd = '', string $type ='sms', array $data = []): void
    {
        $param = $this->moonLetterProvider->makeMessage($sendMsg, $receiverNb, $templateCd, $type, $data);
        $sms = $this->entityMapper->mapper(SmsHistory::class,$param);
        $this->persistFlush($sms);
    }


}