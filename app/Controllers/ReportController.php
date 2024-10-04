<?php
/** @noinspection PhpUnusedParameterInspection */

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Controller;
use App\Core\Helpers\DateHelper;
use App\Services\Report\ReportAreaService;
use App\Services\Report\ReportDateService;
use App\Services\Report\ReportDeviceService;
use App\Services\Report\ReportTimeService;
use App\Services\Report\ReportWeekService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\Twig;

class ReportController extends Controller
{
    public function __construct(
        private readonly Twig $twig,
        private readonly ReportDateService $reportDateService,
        private readonly ReportTimeService $reportTimeService,
        private readonly ReportWeekService $reportWeekService,
        private readonly ReportAreaService $reportAreaService,
        private readonly ReportDeviceService $reportDeviceService,
    ) {
    }


    public function date(Request $request, Response $response, array $args = []): Response
    {
        $params = $this->commonParams($request);
        $startDate =$params['startDate'] ;
        $endDate =  $params['endDate'] ;

        return $this->render($this->twig,$response,"report/date.twig",[
            'list' => $this->reportDateService->reportDate($startDate, $endDate),
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }


    public function time(Request $request, Response $response, array $args = []): Response
    {
        $params = $this->commonParams($request);
        $startDate =$params['startDate'] ;
        $endDate =  $params['endDate'] ;

        return $this->render($this->twig,$response,"report/time.twig",[
            'list' => $this->reportTimeService->reportTime($startDate, $endDate),
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function week(Request $request, Response $response, array $args = []): Response
    {
        $params = $this->commonParams($request);
        $startDate =$params['startDate'] ;
        $endDate =  $params['endDate'] ;

        return $this->render($this->twig,$response,"report/week.twig",[
            'list' => $this->reportWeekService->reportWeek($startDate, $endDate),
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function area(Request $request, Response $response, array $args = []): Response
    {
        $params = $this->commonParams($request);
        $startDate =$params['startDate'] ;
        $endDate =  $params['endDate'] ;

        return $this->render($this->twig,$response,"report/area.twig",[
            'list' => $this->reportAreaService->reportArea($startDate, $endDate),
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }

    public function device(Request $request, Response $response, array $args = []): Response
    {
        $params = $this->commonParams($request);
        $startDate = $params['startDate'] ;
        $endDate =  $params['endDate'] ;

        return $this->render($this->twig,$response,"report/device.twig",[
            'list' => $this->reportDeviceService->reportDevice($startDate, $endDate),
            'startDate' => $startDate,
            'endDate' => $endDate,
        ]);
    }


    public function commonParams(Request $request) : array
    {
        $params = $request->getQueryParams();
        $startDate = empty($params['startDate']) ? DateHelper::getDateYmd('-7') : $params['startDate'] ;
        $endDate = empty($params['endDate']) ? DateHelper::getDateYmd('-1') : $params['endDate'] ;

        if($endDate >= date('Y-m-d')){
            $endDate =  DateHelper::getDateYmd('-1');
        }

        if($startDate >= date('Y-m-d')){
            $startDate =  DateHelper::getDateYmd('-7');
        }
        return [
            'startDate' => $startDate,
            'endDate' => $endDate,
        ];
    }
}