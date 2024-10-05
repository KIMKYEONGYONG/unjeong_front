<?php

declare(strict_types=1);

use App\Controllers\MainController;
use App\Controllers\Menu1Controller;
use App\Controllers\Menu2Controller;
use App\Controllers\Menu3Controller;
use App\Controllers\Menu4Controller;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function(App $app) {

    $app->get('/auth/login',[MainController::class,'loginView'])->add(GuestMiddleware::class);
    $app->get('/auth/logout', [MainController::class, 'logOut'])->add(AuthMiddleware::class);
    $app->get('/', [MainController::class, 'index']);


    $app->group('',function (RouteCollectorProxy $front){

        //
        $front->group('/menu1',function (RouteCollectorProxy $menu1){
            $menu1->get('/business',[Menu1Controller::class,'business']);
            $menu1->get('/architechture',[Menu1Controller::class,'architechture']);
            $menu1->get('/location',[Menu1Controller::class,'location']);
        });

        //
        $front->group('/menu2',function (RouteCollectorProxy $menu2){
            $menu2->get('/intro',[Menu2Controller::class,'intro']);
            $menu2->get('/premium10',[Menu2Controller::class,'premium10']);
        });

        //
        $front->group('/menu3',function (RouteCollectorProxy $menu3){
            $menu3->get('/complex',[Menu3Controller::class,'complex']);
            $menu3->get('/units',[Menu3Controller::class,'units']);
            $menu3->get('/community',[Menu3Controller::class,'community']);
            $menu3->get('/garden',[Menu3Controller::class,'garden']);
            $menu3->get('/system',[Menu3Controller::class,'system']);
        });

        $front->group('/menu4',function (RouteCollectorProxy $menu4){
            $menu4->get('/floorplanA',[Menu4Controller::class,'floorplanA']);
            $menu4->get('/floorplanB',[Menu4Controller::class,'floorplanB']);
            $menu4->get('/floorplanB1',[Menu4Controller::class,'floorplanB1']);
            $menu4->get('/interior',[Menu4Controller::class,'interior']);
            $menu4->get('/interior-bed',[Menu4Controller::class,'interior_bed']);
            $menu4->get('/interior-dining',[Menu4Controller::class,'interior_dining']);
            $menu4->get('/interior-bath',[Menu4Controller::class,'interior_bath']);
            $menu4->get('/interior-etc',[Menu4Controller::class,'interior_etc']);
        });

    });


    $app->group('/api',function (RouteCollectorProxy $action){


    });


};