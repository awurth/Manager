<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\Form\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;

final class CredentialsAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'credentials';

    protected function configureBatchActions($actions): array
    {
        return [];
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('name')
            ->add('username')
            ->add('email', EmailType::class, [
                'required' => false
            ])
            ->add('password')
            ->add('website', UrlType::class, [
                'required' => false
            ])
            ->add('description');
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
            ->addIdentifier('username', null, [
                'header_class' => 'text-center',
                'row_align' => 'center'
            ])
            ->addIdentifier('email', null, [
                'header_class' => 'text-center',
                'row_align' => 'center'
            ])
            ->addIdentifier('website', null, [
                'header_class' => 'text-center',
                'row_align' => 'center'
            ])
            ->addIdentifier('description', null, [
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
