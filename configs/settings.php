<?php

declare(strict_types=1);

use App\Enum\AppEnvironment;

$appEnv       = $_ENV['APP_ENV'] ?? AppEnvironment::Production->value;
$appSnakeName = strtolower(str_replace(' ', '_', $_ENV['APP_NAME']));
$isProduction = AppEnvironment::isProduction($appEnv);


$doctrineConfig = require 'settings/db/doctrine.php';

return [
    'app_name'              => $_ENV['APP_NAME'],
    'app_version'           => $_ENV['APP_VERSION'] ?? '1.0',
    'app_environment'       => $appEnv,
    'display_error_details' => (bool) ($_ENV['APP_DEBUG'] ?? 0),
    'log_errors'            => true,
    'log_error_details'     => true,
    'upload'                => [
        'deny_extension'=> ['php', 'phps', 'php3', 'php4', 'php5', 'php7', 'pht', 'phtml', 'exe', 'html', 'htm', 'htaccess','ini','py'],
        'max_count'     => 10,
        'max_size'      => 200,
        'path_temp_dir'  => STORAGE_PATH . '/upload/tmp'
    ],
    'url'                   => [
        'file_upload'     => '/readFile' ,
    ],
    'doctrine'              => $doctrineConfig($appEnv),
    'session'               => [
        'name'       => $appSnakeName . '_session',
        'flash_name' => $appSnakeName . '_flash',
        'secure'     => false,
        'httponly'   => true,
        'samesite'   => 'lax',
    ],
    'file' => [
        'mode' => FILE_MODE,
        'key' => FILE_KEY,
        'iv' => FILE_IV,
    ]
];