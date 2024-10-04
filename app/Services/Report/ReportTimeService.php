<?php

declare(strict_types=1);

namespace App\Services\Report;

use App\Core\EntityManager\DefaultEntityManager;
use App\Core\JsonFormatter;
use App\Entity\Report\ReportTime;

class ReportTimeService
{
    public function __construct(
        private readonly DefaultEntityManager $entityManager,

    ){}

    public function reportTime(string $startDate, string $endDate) : array
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder
            ->select("
                 RT.reportTime
                ,SUM(RT.uvPageViews) AS uvPageViews
                ,SUM(RT.rvPageViews) AS rvPageViews
                ,SUM(RT.uniqueViews) AS uniqueViews
                ,SUM(RT.reViews) AS reViews
            ")
            ->from(ReportTime::class,'RT')
            ->where('RT.reportDate >= :startDate')
            ->andWhere('RT.reportDate <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->groupBy('RT.reportTime')
            ->orderBy('RT.reportTime', 'asc')
        ;
        $result = $builder->getQuery()->getArrayResult();
        $pvTotal = 0;
        $uvTotal = 0;

        $listData = [];
        $pvCnt = $uvCnt = $time = [];
        foreach ($result as $item){
            $uvPageView = (int)$item['uvPageViews'];
            $rvPageViews = (int)$item['rvPageViews'];
            $uniqueViews = (int)$item['uniqueViews'];
            $reViews = (int)$item['reViews'];
            $reportTime = $item['reportTime'];
            $pv = $uvPageView + $rvPageViews;

            $pvTotal += $pv;
            $uv = $uniqueViews + $reViews;
            $uvTotal += $uv;

            $data = [
                'reportTime' => $reportTime,
                'pv' => $pv,
                'uv' => $uniqueViews,
                'rv' => $reViews,
            ];
            $listData[] = $data;

            $pvCnt[] = $pv;
            $uvCnt[] = $uv;
            $time[] = $reportTime;
        }

        $list['pvTotal'] = $pvTotal;
        $list['uvTotal'] = $uvTotal;
        $list['lists'] = $listData;

        $list['pvJson'] = JsonFormatter::encode($pvCnt);
        $list['uvJson'] = JsonFormatter::encode($uvCnt);
        $list['reportTime'] = JsonFormatter::encode($time);
        return $list;

    }


}