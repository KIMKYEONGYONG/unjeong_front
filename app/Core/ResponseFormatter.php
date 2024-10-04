<?php

declare(strict_types=1);

namespace App\Core;

use App\DataObjects\ResponseData;
use Psr\Http\Message\ResponseInterface;

class ResponseFormatter
{
    public function asJson(
        ResponseInterface $response,
        mixed             $data,
        int               $flags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_APOS | JSON_THROW_ON_ERROR
    ): ResponseInterface
    {

        $modiResponse = $response->withHeader('Content-Type', 'application/json');
        $modiResponse->getBody()->write(json_encode($data, $flags));

        return $modiResponse;
    }

    public function returnResponseData(
        ResponseInterface $response,
        ResponseData $responseData,
        int $flags = JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_QUOT | JSON_HEX_APOS | JSON_THROW_ON_ERROR
    ): ResponseInterface {

        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($responseData->getResponse(), $flags));

        return $response;
    }

}