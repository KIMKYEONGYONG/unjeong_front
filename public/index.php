<?php

declare(strict_types=1);

use DI\DependencyException;
use DI\NotFoundException;
use Slim\App;

$container = require __DIR__ . '/../bootstrap.php';

/** @var App $app */
$app = $container->get(App::class);

try {

    /** @var App $app */
    $app = $container->get(App::class);
    $app->run();

} catch (DependencyException|NotFoundException $e) {

}