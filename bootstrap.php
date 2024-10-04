<?php

declare(strict_types=1);

use Dotenv\Dotenv;
use Valitron\Validator as V;

ini_set('memory_limit','-1');
date_default_timezone_set('Asia/Seoul');

require __DIR__ . '/vendor/autoload.php';
require __DIR__ . '/configs/constants/path.php';
require __DIR__ . '/configs/constants/value.php';
require __DIR__ . '/configs/constants/url.php';

define("AppEntityContainer", require CONFIG_PATH . '/container/container.php');

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

V::langDir(__DIR__ . '/vendor/vlucas/valitron/lang');
V::lang('ko');

V::addRule('cellphone', static function ($field, $value, array $params, array $fields){
    if(empty($value)) {
        return false;
    }
    return preg_match('/^(010|011|016|017|018|019)\d{3,4}\d{4}$/', str_replace('-','',$value));
},'{field}를 확인 하시기 바랍니다');


return require CONFIG_PATH . '/container/container.php';