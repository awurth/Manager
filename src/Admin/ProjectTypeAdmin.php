<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

final class ProjectTypeAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'project-type';

    protected function configureBatchActions($actions): array
    {
        return [];
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper->add('name');
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', null, [
                'header_class' => 'text-center',
                'row_align' => 'center'
            ])
            ->addIdentifier('name', null, [
                'header_class' => 'text-center',
                'row_align' => 'center'
            ]);
    }

    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection
            ->remove('batch')
            ->remove('export')
            ->remove('show');
    }
}
