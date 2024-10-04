<?php

declare(strict_types=1);

namespace App\Core;

use App\Exception\MapperException;
use App\Interfaces\EntityInterface;
use ReflectionClass;
use ReflectionEnum;
use ReflectionException;
use ReflectionNamedType;
use RuntimeException;

class EntityMapper
{
    /**
     * @throws ReflectionException | RuntimeException
     */
    public function mapper(string|EntityInterface $entity,array $data,array $hashChangeFields = [],array $exceptKey = []): EntityInterface
    {
        if (is_string($entity) && !class_exists($entity)) {
            throw new MapperException("not exists class => $entity");
        }
        $class = is_object($entity) ? $entity : new $entity();
        $reflectionClass = new ReflectionClass($class);

        foreach ($reflectionClass->getProperties() as $property) {
            $propertyType = $property->getType();
            $propertyName = $property->getName();
            if (!$propertyType instanceof ReflectionNamedType) {
                continue;
            }
            if (array_key_exists($propertyName, $data)) {
                $value = $propertyType->getName() === 'string' ? trim((string)$data[$propertyName]) : $data[$propertyName];
                if (empty($value) && in_array($propertyName,$exceptKey,true)) {
                    continue;
                }
                if (enum_exists($propertyType->getName())) {
                    $rEnum = new ReflectionEnum($propertyType->getName());
                    if ((string) $rEnum->getBackingType() === 'int') {
                        $value = (int) $value;
                    }
                    foreach ($rEnum->getCases() as $case) {
                        if ($case->getBackingValue() === $value) {
                            $value = $case->getValue();
                            break;
                        }
                    }
                } elseif (in_array($propertyName, $hashChangeFields, true)) {
                    if (!empty($value)) {
                        $value = password_hash($value, PASSWORD_BCRYPT, ['cost' => 12]);
                    }
                }
                if ($propertyType->getName() === 'int') {
                    $value = (int)$value;
                }
                if ($propertyType->getName() === 'float') {
                    $value = (float)$value;
                }

                $property->setValue($class, $value);
            } elseif (is_object($entity)) {
                $property->setValue($class, $property->getValue($entity));
            }
            $error = error_get_last();
            if ($error) {
                throw new RuntimeException($error['message']);
            }
        }
        return $class;
    }
}