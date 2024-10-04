<?php

declare(strict_types=1);

namespace App\Services\Report;

use App\Core\EntityManager\DefaultEntityManager;
use App\Core\JsonFormatter;
use App\Entity\Report\ReportDevice;

class ReportDeviceService
{
    public function __construct(
        private readonly DefaultEntityManager $entityManager,

    ){}

    public function reportDevice(string $startDate, string $endDate) : array
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder
            ->select("
                 RD.device
                ,SUM(RD.uvPageViews) AS uvPageViews
                ,SUM(RD.rvPageViews) AS rvPageViews
                ,SUM(RD.uniqueViews) AS uniqueViews
                ,SUM(RD.reViews) AS reViews
            ")
            ->from(ReportDevice::class,'RD')
            ->where('RD.reportDate >= :startDate')
            ->andWhere('RD.reportDate <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->groupBy('RD.device')
            ->orderBy('RD.device', 'desc')
        ;
        $result = $builder->getQuery()->getArrayResult();
        $pvTotal = 0;
        $uvTotal = 0;

        $listData = [];
        $pvCnt = $uvCnt = $deviceList = [];
        foreach ($result as $item){
            $uvPageView = (int)$item['uvPageViews'];
            $rvPageViews = (int)$item['rvPageViews'];
            $uniqueViews = (int)$item['uniqueViews'];
            $reViews = (int)$item['reViews'];
            $device = $item['device'];
            $pv = $uvPageView + $rvPageViews;

            $pvTotal += $pv;
            $uv = $uniqueViews + $reViews;
            $uvTotal += $uv;

            $deviceString = match ($device){
                'P' => 'pc',
                'M' => 'mobile',
                default => '',
            };

            $data = [
                'device' => $deviceString,
                'pv' => $pv,
                'uv' => $uniqueViews,
                'rv' => $reViews,
            ];
            $listData[] = $data;

            $pvCnt[] = $pv;
            $uvCnt[] = $uv;
            $deviceList[] = $deviceString;
        }

        $list['pvTotal'] = $pvTotal;
        $list['uvTotal'] = $uvTotal;
        $list['lists'] = $listData;

        $list['pvJson'] = JsonFormatter::encode($pvCnt);
        $list['uvJson'] = JsonFormatter::encode($uvCnt);
        $list['device'] = JsonFormatter::encode($deviceList);
        return $list;

    }


}