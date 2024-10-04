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

    $app->post('/action/login',[ActionAuthController::class,'login']);

    $app->group('',function (RouteCollectorProxy $master){
        // 관심고객관리
        $master->group('/apply',function (RouteCollectorProxy $apply){
            // 관심고객목록
            $apply->group('/client',function (RouteCollectorProxy $client){
                $client->get('/list',[ClientController::class,'list']);
            });

            // 문자발송관리
            $apply->group('/sms',function (RouteCollectorProxy $sms){
                $sms->get('/list',[SmsController::class,'list']);
            });
        });

        // 게시판 관리
        $master->group('/board',function (RouteCollectorProxy $board){
            $board->get('/{code}/list',[BoardController::class,'list']);
            $board->get('/{code}/register[/{id:[0-9]+}]',[BoardController::class,'register']);
        });


    })->add(AuthMiddleware::class);


    $app->group('/api',function (RouteCollectorProxy $api){
        // 공지사항
        $api->group('/board',function (RouteCollectorProxy $board){
            $board->get('/{code}/list',[ActionBoardController::class,'list']);
            $board->get('/detail/{id:[0-9]+}',[ActionBoardController::class,'detail']);
        });

        // 관심고객 등록
        $api->options('/apply/client/register',[ActionClientController::class,'register']);

    });

    // 파일 읽는 라우터
    // code 파라메터
    $app->get('/readFile',[ReadFileController::class,'readFile']);
};