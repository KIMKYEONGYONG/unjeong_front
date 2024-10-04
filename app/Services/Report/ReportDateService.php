<?php

declare(strict_types=1);

namespace App\Services\Report;

use App\Core\EntityManager\DefaultEntityManager;
use App\Core\JsonFormatter;
use App\Entity\Report\ReportDate;

class ReportDateService
{
    public function __construct(
        private readonly DefaultEntityManager $entityManager,

    ){}

    public function reportDate(string $startDate, string $endDate) : array
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder
            ->select("
                 RD.reportDate
                ,RD.uvPageViews
                ,RD.rvPageViews
                ,RD.uniqueViews
                ,RD.reViews
            ")
            ->from(ReportDate::class,'RD')
            ->where('RD.reportDate >= :startDate')
            ->andWhere('RD.reportDate <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->orderBy('RD.reportDate', 'desc')
        ;
        $result = $builder->getQuery()->getArrayResult();
        $pvTotal = 0;
        $uvTotal = 0;

        $listData = [];
        $pvCnt = $uvCnt = $date = [];
        foreach ($result as $item){
            $uvPageView = (int)$item['uvPageViews'];
            $rvPageViews = (int)$item['rvPageViews'];
            $uniqueViews = (int)$item['uniqueViews'];
            $reViews = (int)$item['reViews'];
            $reportDate = $item['reportDate'];
            $pv = $uvPageView + $rvPageViews;

            $pvTotal += $pv;
            $uv = $uniqueViews + $reViews;
            $uvTotal += $uv;

            $data = [
                'reportDate' => $reportDate,
                'pv' => $pv,
                'uv' => $uniqueViews,
                'rv' => $reViews,
            ];
            $listData[] = $data;

            $pvCnt[] = $pv;
            $uvCnt[] = $uv;
            $date[] = date("m/d", strtotime($reportDate));
        }

        $list['pvTotal'] = $pvTotal;
        $list['uvTotal'] = $uvTotal;
        $list['lists'] = $listData;

        $list['pvJson'] = JsonFormatter::encode(array_reverse($pvCnt));
        $list['uvJson'] = JsonFormatter::encode(array_reverse($uvCnt));
        $list['reportDate'] = JsonFormatter::encode(array_reverse($date));
        return $list;

    }


}