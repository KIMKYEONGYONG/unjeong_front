<?php

declare(strict_types=1);

namespace App\Services\Report;

use App\Core\EntityManager\DefaultEntityManager;
use App\Core\JsonFormatter;
use App\Entity\Report\ReportArea;

class ReportAreaService
{
    public function __construct(
        private readonly DefaultEntityManager $entityManager,

    ){}

    public function reportArea(string $startDate, string $endDate) : array
    {
        $builder = $this->entityManager->createQueryBuilder();
        $builder
            ->select("
                 A.city
                ,A.gu
                ,RA.areaCode
                ,SUM(RA.uvPageViews) AS uvPageViews
                ,SUM(RA.rvPageViews) AS rvPageViews
                ,SUM(RA.uniqueViews) AS uniqueViews
                ,SUM(RA.reViews) AS reViews
            ")
            ->from(ReportArea::class,'RA')
            ->leftJoin('RA.area','A')
            ->where('RA.reportDate >= :startDate')
            ->andWhere('RA.reportDate <= :endDate')
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate)
            ->groupBy('RA.areaCode')
            ->orderBy('SUM(RA.uvPageViews) + SUM(RA.rvPageViews)', 'desc')
            ->addOrderBy('RA.areaCode', 'desc')
        ;
        $result = $builder->getQuery()->getArrayResult();
        $pvTotal = 0;
        $uvTotal = 0;

        $listData = [];
        $pvCnt = $uvCnt = $address = [];
        foreach ($result as $item){
            $uvPageView = (int)$item['uvPageViews'];
            $rvPageViews = (int)$item['rvPageViews'];
            $uniqueViews = (int)$item['uniqueViews'];
            $reViews = (int)$item['reViews'];
            $city = $item['city'];
            $gu = $item['gu'];

            $areaCode = $item['areaCode'];

            $dataAddress = $city.' '.$gu;
            if(empty(trim($dataAddress))){
                $dataAddress = "해외($areaCode)";
            }
            $pv = $uvPageView + $rvPageViews;

            $pvTotal += $pv;
            $uv = $uniqueViews + $reViews;
            $uvTotal += $uv;



            $data = [
                'address' => $dataAddress,
                'pv' => $pv,
                'uv' => $uniqueViews,
                'rv' => $reViews,
            ];
            $listData[] = $data;

            $pvCnt[] = $pv;
            $uvCnt[] = $uv;
            $address[] = $dataAddress;
        }

        $list['pvTotal'] = $pvTotal;
        $list['uvTotal'] = $uvTotal;
        $list['lists'] = $listData;

        $pvCnt = array_slice($pvCnt, 0, 5);
        $uvCnt = array_slice($uvCnt, 0, 5);
        $address = array_slice($address, 0, 5);

        $list['pvJson'] = JsonFormatter::encode($pvCnt);
        $list['uvJson'] = JsonFormatter::encode($uvCnt);
        $list['address'] = JsonFormatter::encode($address);
        return $list;

    }


}