<?php

declare(strict_types=1);

use App\Controllers\MainController;
use App\Controllers\Menu1Controller;
use App\Controllers\Menu2Controller;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function(App $app) {

    $app->get('/auth/login',[MainController::class,'loginView'])->add(GuestMiddleware::class);
    $app->get('/auth/logout', [MainController::class, 'logOut'])->add(AuthMiddleware::class);
    $app->get('/', [MainController::class, 'index']);


    $app->group('',function (RouteCollectorProxy $front){

        // 관심고객관리
        $front->group('/menu1',function (RouteCollectorProxy $menu1){
            $menu1->get('/business',[Menu1Controller::class,'business']);
            $menu1->get('/architechture',[Menu1Controller::class,'architechture']);
            $menu1->get('/location',[Menu1Controller::class,'location']);
        });

        // 관심고객관리
        $front->group('/menu2',function (RouteCollectorProxy $menu2){
            $menu2->get('/intro',[Menu2Controller::class,'intro']);
            $menu2->get('/premium10',[Menu2Controller::class,'premium10']);
        });

    });


    $app->group('/api',function (RouteCollectorProxy $action){


    });


};