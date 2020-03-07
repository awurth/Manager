<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

final class ServerUserAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'server-user';

    protected function configureBatchActions($actions): array
    {
        return [];
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('username')
            ->add('password');
    }

    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection->clearExcept(['create', 'edit']);
    }
}
