<?php

declare(strict_types=1);

namespace App\Services\Report;

use App\Core\EntityManager\DefaultEntityManager;
use App\Core\JsonFormatter;
use App\Entity\Report\ReportWeek;

class ReportWeekService
{
    public function __construct(
        private readonly DefaultEntityManager $entityManager,

    ){}

    public function reportWeek(string $startDate, string $endDate) : array
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder
            ->select("
                 RW.reportWeek
                ,SUM(RW.uvPageViews) AS uvPageViews
                ,SUM(RW.rvPageViews) AS rvPageViews
                ,SUM(RW.uniqueViews) AS uniqueViews
                ,SUM(RW.reViews) AS reViews
            ")
            ->from(ReportWeek::class,'RW')
            ->where('RW.reportDate >= :startDate')
            ->andWhere('RW.reportDate <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->groupBy('RW.reportWeek')
            ->orderBy('RW.reportWeek', 'asc')
        ;
        $result = $builder->getQuery()->getArrayResult();
        $pvTotal = 0;
        $uvTotal = 0;

        $listData = [];
        $pvCnt = $uvCnt = $week = [];
        foreach ($result as $item){
            $uvPageView = (int)$item['uvPageViews'];
            $rvPageViews = (int)$item['rvPageViews'];
            $uniqueViews = (int)$item['uniqueViews'];
            $reViews = (int)$item['reViews'];
            $reportWeek = $item['reportWeek'];
            $pv = $uvPageView + $rvPageViews;

            $pvTotal += $pv;
            $uv = $uniqueViews + $reViews;
            $uvTotal += $uv;

            $reportWeekString = match ($reportWeek){
                '1' => '월요일',
                '2' => '화요일',
                '3' => '수요일',
                '4' => '목요일',
                '5' => '금요일',
                '6' => '토요일',
                '0' => '일요일',
                default => '',
            };

            $data = [
                'reportWeek' => $reportWeekString,
                'pv' => $pv,
                'uv' => $uniqueViews,
                'rv' => $reViews,
            ];
            $listData[] = $data;

            $pvCnt[] = $pv;
            $uvCnt[] = $uv;
            $week[] = $reportWeekString;
        }

        $list['pvTotal'] = $pvTotal;
        $list['uvTotal'] = $uvTotal;
        $list['lists'] = $listData;

        $list['pvJson'] = JsonFormatter::encode($pvCnt);
        $list['uvJson'] = JsonFormatter::encode($uvCnt);
        $list['reportWeek'] = JsonFormatter::encode($week);
        return $list;

    }


}