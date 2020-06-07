<?php

namespace App\Action;

use Doctrine\ORM\QueryBuilder;
use Lexik\Bundle\FormFilterBundle\Filter\FilterBuilderUpdaterInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;

trait FilterTrait
{
    /**
     * @var FilterBuilderUpdaterInterface
     */
    protected $filterBuilderUpdater;

    protected function filter(QueryBuilder $queryBuilder, FormInterface $form, Request $request): void
    {
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $this->filterBuilderUpdater->addFilterConditions($form, $queryBuilder);
        }
    }

    /**
     * @required
     */
    public function setFilterBuilderUpdater(FilterBuilderUpdaterInterface $filterBuilderUpdater): void
    {
        $this->filterBuilderUpdater = $filterBuilderUpdater;
    }
}
