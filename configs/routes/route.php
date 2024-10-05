<?php

declare(strict_types=1);

use App\Controllers\Action\ActionAuthController;
use App\Controllers\Action\ActionBoardController;
use App\Controllers\Action\ActionClientController;
use App\Controllers\Action\ActionSmsController;
use App\Controllers\BoardController;
use App\Controllers\ClientController;
use App\Controllers\MainController;
use App\Controllers\ReadFileController;
use App\Controllers\ReportController;
use App\Controllers\SmsController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function(App $app) {

    $app->get('/auth/login',[MainController::class,'loginView'])->add(GuestMiddleware::class);
    $app->get('/auth/logout', [MainController::class, 'logOut'])->add(AuthMiddleware::class);
    $app->get('/', [MainController::class, 'index']);


    $app->group('',function (RouteCollectorProxy $front){



    });


    $app->group('/api',function (RouteCollectorProxy $action){


    });


};