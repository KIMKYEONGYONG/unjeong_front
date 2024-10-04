<?php

declare(strict_types=1);

namespace App\Helper;

use App\Traits\DataProcessTrait;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator as OrmPaginator;
use Psr\Http\Message\ServerRequestInterface;

class PaginatorHelper
{
    use DataProcessTrait;

    private int $total;
    private int $lastPage;
    private int $page;
    private int $blockStart;
    private int $blockEnd;
    private int $limit = DEFAULT_PAGE_SIZE;
    private OrmPaginator $items;


    public function paginate(QueryBuilder $query, ServerRequestInterface $request, int $pageBlock = 5, int $pageSize = DEFAULT_PAGE_SIZE): PaginatorHelper
    {
        $this->page = $this->dataKeyExistsAndSetDefault('page',$this->getRequestData($request),1);
        $this->limit = $this->dataKeyExistsAndSetDefault('pageSize',$this->getRequestData($request),$pageSize);

        if ($this->page === 0) {
            $this->page = 1;
        }
        if ($this->limit === 0) {
            $this->limit = $pageSize;
        }
        $paginator = new OrmPaginator($query);
        $paginator
            ->getQuery()
            ->setFirstResult($this->limit * ($this->page - 1))
            ->setMaxResults($this->limit);
        $this->total = $paginator->count();
        $this->lastPage = (int) ceil($this->total / $paginator->getQuery()->getMaxResults());


        if ($this->page > $this->lastPage ) {
            $this->page = $this->lastPage;
        }

        if (($this->page % $pageBlock) === 0) {
            $this->blockStart = $this->page - ($pageBlock - 1);
        }
        else {
            $this->blockStart = (int)floor($this->page / $pageBlock) * $pageBlock + 1;
        }

        $this->blockEnd = ($this->blockStart  + $pageBlock) - 1;
        if ($this->blockEnd > $this->lastPage) {
            $this->blockEnd = $this->lastPage;
        }

        $this->items = $paginator;

        return $this;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getLastPage(): int
    {
        return $this->lastPage;
    }

    public function getItems():OrmPaginator
    {
        return $this->items;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getBlockStart(): int
    {
        return $this->blockStart;
    }

    public function getBlockEnd(): int
    {
        return $this->blockEnd;
    }

    public function toArray(PaginatorHelper $helper): array
    {
        return [
            'total' => $helper->getTotal(),
            'lastPage' => $helper->getLastPage(),
            'page' => $helper->getPage(),
            'limit' => $helper->getLimit(),
            'blockStart' => $helper->getBlockStart(),
            'blockEnd' => $helper->getBlockEnd(),
            'items' => $helper->getItems()
        ];
    }


}