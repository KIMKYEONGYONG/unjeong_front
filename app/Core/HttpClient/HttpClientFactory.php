<?php

declare(strict_types=1);

namespace App\Core\HttpClient;

use App\Core\Config;
use App\Core\Exception\HttpClientException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

readonly class HttpClientFactory implements HttpClientFactoryInterface
{
    public function __construct(
        private ContainerInterface $container,
        private Config $config

    ) {
    }
    public function make(array $option = []): HttpClientInterface
    {
        $httpClientClass = $this->config->get('http_client.class');
        try {
            $httpClient = $this->container->get($httpClientClass);
            if ($httpClient instanceof HttpClientInterface) {
                $httpClient->withOption($option);
                return $httpClient;
            }
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface) {

        }
        throw new HttpClientException(
            errors: 'Failed to instantiate class "' . $httpClientClass . '"',
            code: 500
        );
    }
}