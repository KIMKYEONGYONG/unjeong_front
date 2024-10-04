<?php

declare(strict_types=1);

namespace App\DataObjects;

class ResponseData
{
    public bool $result;
    public string $message;

    public function __construct(
    ) {
    }

    public function getResponse(): array
    {
        return [
            'result' => $this->result,
            'message' => $this->message
        ];
    }

    public function setResponse(array $response) : void
    {
        $this->result = $response['result'];
        $this->message = $response['message'];
    }
}