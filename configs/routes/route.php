<?php

declare(strict_types=1);

use App\Controllers\Action\ActionAuthController;
use App\Controllers\Action\ActionClientController;
use App\Controllers\MainController;
use App\Controllers\Menu1Controller;
use App\Controllers\Menu2Controller;
use App\Controllers\Menu3Controller;
use App\Controllers\Menu4Controller;
use App\Controllers\Menu5Controller;
use App\Controllers\Menu6Controller;
use App\Controllers\Menu7Controller;
use App\Controllers\Menu8Controller;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function(App $app) {

    $app->get('/auth/logout', [MainController::class, 'logOut'])->add(AuthMiddleware::class);
    $app->get('/', [MainController::class, 'index']);
    $app->get('/wip', [MainController::class, 'wip']);

    $app->post('/action/login',[ActionAuthController::class,'login']);


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
            $menu4->get('/interior_bed',[Menu4Controller::class,'interior_bed']);
            $menu4->get('/interior_dining',[Menu4Controller::class,'interior_dining']);
            $menu4->get('/interior_bath',[Menu4Controller::class,'interior_bath']);
            $menu4->get('/interior_etc',[Menu4Controller::class,'interior_etc']);
        });

        $front->group('/menu5',function (RouteCollectorProxy $menu5){
            $menu5->get('/news',[Menu5Controller::class,'news']);
            $menu5->get('/videos',[Menu5Controller::class,'videos']);
            $menu5->get('/vr',[Menu5Controller::class,'vr']);
        });

        $front->group('/menu6',function (RouteCollectorProxy $menu6){
            $menu6->get('/notice/list',[Menu6Controller::class,'notice']);
            $menu6->get('/notice/detail/{id:[0-9]+}',[Menu6Controller::class,'noticeDetail']);
            $menu6->get('/reg',[Menu6Controller::class,'reg']);
        });

        $front->group('/menu7',function (RouteCollectorProxy $menu7){
            $menu7->get('/login',[Menu7Controller::class,'login'])->add(GuestMiddleware::class);
            $menu7->get('/idFind',[Menu7Controller::class,'idFind'])->add(GuestMiddleware::class);
            $menu7->get('/idFind2',[Menu7Controller::class,'idFind2'])->add(GuestMiddleware::class);
        });

        $front->group('/menu8',function (RouteCollectorProxy $menu8){

            $menu8->get('/signup_terms',[Menu8Controller::class,'signup_terms'])->add(GuestMiddleware::class);
            $menu8->get('/signup',[Menu8Controller::class,'signup'])->add(GuestMiddleware::class);
            $menu8->get('/signup_complete',[Menu8Controller::class,'signup_complete'])->add(GuestMiddleware::class);
            $menu8->get('/member_notice/list',[Menu8Controller::class,'notice'])->add(AuthMiddleware::class);
            $menu8->get('/member_notice/detail/{id:[0-9]+}',[Menu8Controller::class,'noticeDetail'])->add(AuthMiddleware::class);

        });

    });


    $app->group('/action',function (RouteCollectorProxy $action){
        $action->post('/apply/client/register',[ActionClientController::class,'register']);

    });


};