<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\Form\Type\CollectionType;

final class ServerAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'server';

    protected function configureBatchActions($actions): array
    {
        return [];
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('Server', ['label' => 'form.group.label_server'])
                ->add('name')
                ->add('ip')
                ->add('operatingSystem')
            ->end()
            ->with('Users', ['label' => 'form.group.label_server_users'])
                ->add('users', CollectionType::class, [
                    'by_reference' => false,
                    'label' => false
                ], [
                    'edit' => 'inline',
                    'inline' => 'table'
                ])
            ->end()
            ->with('ProjectEnvironments', ['label' => 'form.group.label_project_environments'])
                ->add('projectEnvironments', CollectionType::class, [
                    'by_reference' => false,
                    'label' => false
                ], [
                    'edit' => 'inline',
                    'inline' => 'table'
                ])
            ->end();
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
            ])
            ->addIdentifier('ip', null, [
                'header_class' => 'text-center',
                'row_align' => 'center'
            ])
            ->addIdentifier('operatingSystem', null, [
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
