<?php

declare(strict_types=1);

namespace App\Helper;

use App\Interfaces\EntityInterface;
use App\Traits\DataProcessTrait;
use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\QueryBuilder;
use Psr\Http\Message\ServerRequestInterface;

class QueryConditionHelper
{
    use DataProcessTrait;
    public function dateCondition(QueryBuilder $query,string $startDateKey, string $endDateKey , string $field, ServerRequestInterface $request): QueryBuilder
    {
        $values = $this->getRequestData($request);
        if (!empty($values[$startDateKey])) {
            $query->andWhere($field . ' >= :startDate');
            $query->setParameter('startDate',$values[$startDateKey]);
        }

        if (!empty($values[$endDateKey])) {
            $query->andWhere($field . ' < :endDate');
            $endDate = Carbon::createFromFormat('Y-m-d',$values[$endDateKey])
                ->addDay()
                ->toDateString();
            $query->setParameter('endDate',$endDate);
        }
        return $query;
    }

    public function keywordCondition(QueryBuilder $query, array $fields, ServerRequestInterface $request,bool $isLikeSearch = true, string $key = 'keyword'): QueryBuilder
    {
        $values = $this->getRequestData($request);
        if (!empty($values[$key])) {
            $condition = [];
            $expr = $isLikeSearch? ' LIKE ' : ' = ';
            $keyword = $isLikeSearch? '%'.$values[$key].'%' : $values[$key];

            foreach ($fields as $field) {
                $condition[] = $field . $expr . ':keyword';
            }

            $query->andWhere(implode(' OR ',$condition));
            $query->setParameter('keyword',$keyword);
        }
        return $query;
    }

    public function eqCondition(QueryBuilder $query,array $fieldDatas, ServerRequestInterface $request): QueryBuilder
    {
        $values = $this->getRequestData($request);
        foreach ($fieldDatas as $column => $queryStringVal) {
            if(array_key_exists($queryStringVal, $values) && trim($values[$queryStringVal]) !== '') {
                $query->andWhere($column . " = :$queryStringVal");
                $query->setParameter($queryStringVal,$values[$queryStringVal]);
            }
        }
        return $query;
    }

    public function orderByCondition(QueryBuilder $query, array $fieldDatas, ServerRequestInterface $request): QueryBuilder
    {
        $values = $this->getRequestData($request);
        foreach ($fieldDatas as $column => $queryStringVal) {
            if(array_key_exists($queryStringVal, $values) && trim($values[$queryStringVal]) !== '') {
                $query->orderby($column, $values[$queryStringVal]);
            }
        }
        return $query;
    }
}