<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\Form\Type\CollectionType;
use Vich\UploaderBundle\Form\Type\VichImageType;

final class ProjectAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'project';

    protected function configureBatchActions($actions): array
    {
        return [];
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->with('Project', ['label' => 'form.group.label_project'])
                ->add('imageFile', VichImageType::class, ['required' => false])
                ->add('name')
                ->add('slug')
                ->add('customer')
                ->add('type')
                ->add('description')
            ->end()
            ->with('Environments', ['label' => 'form.group.label_environments'])
                ->add('environments', CollectionType::class, [
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
            ->addIdentifier('slug', null, [
                'header_class' => 'text-center',
                'row_align' => 'center'
            ])
            ->addIdentifier('name', null, [
                'header_class' => 'text-center',
                'row_align' => 'center'
            ])
            ->addIdentifier('customer.name', null, [
                'header_class' => 'text-center',
                'row_align' => 'center'
            ])
            ->addIdentifier('type.name', null, [
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
