<?php

declare(strict_types=1);

namespace App\Provider\External;

use App\Core\JsonFormatter;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use http\Exception\RuntimeException;
use JetBrains\PhpStorm\ArrayShape;

class MoonLetterProvider
{
    private string $sendNumber = "15337720";

    private string $baseUrl = "https://api.moonletter.co.kr/api/";

    private array $headers  = [
        'Content-Type' => 'application/json',
        'apiUser' => 'neoebiz',
    ];

    private string $channelId = "@stax";

    public function __construct() {

    }
    private function getClient(string $apiType): Client
    {
        $apiKey = match ($apiType){
          'sms' => '890f6b5085667b281fe5dcf2310977ea23783cfed901a4a88cb9f44c26089ca3',
          'kakao' => '766917ffa09517d9d94c855e8b8218858c3f0d0fbca5f0ff19450c7c2c9f16df'
        };

        $this->headers['apiKey'] = $apiKey;

        return new Client(
            [
                'base_uri'  => $this->baseUrl,
                'timeout'   => 10,
                'headers' => $this->headers
            ]
        );
    }

    /**
     * @throws GuzzleException
     */
    public function makeMessage(string $sendMsg = '', string $receiverNb = '', string $templateCd = '', string $type ='sms', array $data = []): array
    {
        $message = match($type){
            'sms' => $this->makeSendSmSMessage($sendMsg,$receiverNb),
            'kakao' => $this->makeKakaoSendMessage($receiverNb, $templateCd, $data)
        };
        $send = JsonFormatter::decode($this->sendMessage($message, $type));
        return[
            'receiveNum' => $receiverNb,
            'returnCode' => $send['code'] ?? "ERR",
            'returnMsg' => $send['message'] ?? "",
            'messageId' => $send['data']['messageId'] ?? "",
            'sendMsg' => $sendMsg
        ];
    }



    public function sendMessage(array $message, string $type = 'sms'): string
    {
        try {
            $response = $this->getClient($type)->post('v1/' . $type . '/messages', [
                'headers' => $this->headers,
                'json' => $message
            ]);
            return $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            error_log($e->getMessage());
            throw new RuntimeException("test");
        }

    }


    #[ArrayShape([
        'message' => "string",
        'sendNb' => "string",
        'receiveNb' => "string",
        'type' => "string",
        'messageGubun' => "string"
    ])]
    public function makeSendSmSMessage(string $msg, string $recvNumber): array
    {
        return  [
            'message' => $msg ,
            'sendNb' => $this->sendNumber,
            'receiveNb' => $recvNumber,
            'type' => mb_strwidth($msg,mb_internal_encoding()) > 90 ? "LMS" : "SMS",
            'messageGubun' => "N"
        ];
    }

    public function makeKakaoSendMessage(string $receiverNb, string $templateCd, array $data): array
    {
        return  [
            'sendNb' => $this->sendNumber,
            'kakaoType' => "NCT",
            'channelId' => $this->channelId,
            'templateCd' => $templateCd,
            'smsReSendYn' => 'Y',
            'receivers' => [
                [
                    'receiverNb' => $receiverNb,
                    'receiverSubsList' => $this->getTemplateSubList($templateCd, $data)
                ]

            ]
        ];
    }

    public function getTemplateSubList(string $templateCd , array $data) : array
    {
        return match ($templateCd){
            'SJT_082290' => [ // 예시
                [
                    'subWord' => '#{회원명}',
                    'subValue' => $data['name']
                ],
            ]
        };
    }
}