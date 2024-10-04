<?php

declare(strict_types=1);

namespace App\RequestValidators;

use App\Interfaces\RequestValidatorFactoryInterface;
use App\Interfaces\RequestValidatorInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use RuntimeException;

class RequestValidatorFactory implements RequestValidatorFactoryInterface
{
    public function __construct(private readonly ContainerInterface $container)
    {
    }
    public function make(string $class): RequestValidatorInterface
    {
        try {
            $validator = $this->container->get($class);
            if ($validator instanceof RequestValidatorInterface) {
                return $validator;
            }
        } catch (NotFoundExceptionInterface|ContainerExceptionInterface) {
        }

        throw new RuntimeException('Failed to instantiate the request validator class "' . $class . '"');
    }
}