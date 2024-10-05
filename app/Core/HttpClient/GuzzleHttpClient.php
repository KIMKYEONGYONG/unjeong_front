<?php

declare(strict_types=1);

namespace App\Core\HttpClient;


use App\Core\Exception\HttpClientException;
use App\Core\JsonFormatter;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use JsonException;
use Psr\Http\Message\ResponseInterface;

class GuzzleHttpClient implements HttpClientInterface
{
    private Client $client;
    public function withOption(array $option = []) : HttpClientInterface
    {
        $defaultOption = [
            'timeout' => 30
        ];
        $this->client = new Client(array_merge($defaultOption, $option));
        return $this;
    }

    public function request(string $method, string $url, array $options = []): ResponseInterface
    {
        try {
            $response = $this->client->request($method, $url, $options);
            if ($response->getStatusCode() !== 200) {
                throw new HttpClientException(
                    errors: "Api Server Internal Error!!",
                    code: $response->getStatusCode()
                );
            }
            return $response;
        } catch ( GuzzleException $e) {
            $this->handleException($e);
        }
    }

    private function handleException(GuzzleException $e): void
    {
        if ($e instanceof RequestException && $e->hasResponse()) {
            $error = $e->getResponse()?->getBody()->getContents();
            $status = $e->getResponse()?->getStatusCode() ?? 500;
            try {
                $contents = JsonFormatter::decode($error);
                $message = $contents['error']['description'] ?? $e->getMessage();
                $type = $contents['error']['type'] ?? "HTTP_CLIENT_ERROR";
            } catch (JsonException $exception) {
                $message = 'An error occurred while parsing JSON!! -> ' .$exception->getMessage();
                $type = JsonFormatter::jsonErrorType($exception->getCode());
            }

            throw new HttpClientException(errors: $message, type: $type, code: $status);
        }
        throw new HttpClientException(errors: $e->getMessage());
    }
}