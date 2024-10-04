<?php

declare(strict_types=1);

use App\Enum\AppEnvironment;
use DI\ContainerBuilder;

$appEnv = $_ENV['APP_ENV'] ?? AppEnvironment::Production->value;

$containerBuilder = new ContainerBuilder();

/*
if (AppEnvironment::isProduction($appEnv)) {
    $containerBuilder->enableCompilation(STORAGE_PATH . '/cache/di');
}
*/
$containerBuilder->addDefinitions(__DIR__ . '/container_bindings.php');

try {
    return $containerBuilder->build();
} catch (Exception $e) {
}