<?php

declare(strict_types=1);

use App\Enum\AppEnvironment;

return static function($appEnv) {
    return [
        'dev_mode'   => AppEnvironment::isDevelopment($appEnv),
        'cache_dir'  => STORAGE_PATH . '/cache/doctrine',
        'proxy_dir'  => STORAGE_PATH . '/proxy/doctrine',
        'entity_dir' => [APP_PATH . '/Entity'],
        'connections' => [
            'default' => [
                'driver'   => $_ENV['DB_DRIVER'] ?? 'pdo_mysql',
                'host'     => $_ENV['DB_HOST'],
                'port'     => $_ENV['DB_PORT'] ? (int)$_ENV['DB_PORT'] : 3306,
                'dbname'   => $_ENV['DB_NAME'],
                'user'     => $_ENV['DB_USER'],
                'password' => $_ENV['DB_PASS'],
                'charset'  => 'utf8mb4'
            ]
        ]
    ];
};
