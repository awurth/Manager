<?php

namespace App\Action;

use Doctrine\ORM\QueryBuilder;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Pagerfanta;
use Symfony\Component\HttpFoundation\Request;

trait PaginationTrait
{
    protected function paginate(QueryBuilder $queryBuilder, Request $request): Pagerfanta
    {
        $page = $request->query->getInt('page') ?: 1;

        $adapter = new DoctrineORMAdapter($queryBuilder);
        $pager = new Pagerfanta($adapter);

        $pager->setCurrentPage($page);

        return $pager;
    }
}
