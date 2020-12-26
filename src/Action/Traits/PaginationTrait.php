<?php

namespace App\Action\Traits;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Doctrine\ORM\QueryAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

trait PaginationTrait
{
    protected function paginate(QueryBuilder $queryBuilder, Request $request): Pagerfanta
    {
        $page = $request->query->getInt('page') ?: 1;

        $adapter = new QueryAdapter($queryBuilder);
        $pager = new Pagerfanta($adapter);

        $pager->setCurrentPage($page);

        return $pager;
    }
}
