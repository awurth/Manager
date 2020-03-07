<?php

namespace App\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;

final class CustomerContactAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'contact';

    protected function configureBatchActions($actions): array
    {
        return [];
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('firstname')
            ->add('lastname')
            ->add('job')
            ->add('email', EmailType::class, [
                'required' => false
            ])
            ->add('phone', TelType::class, [
                'required' => false
            ]);
    }

    protected function configureListFields(ListMapper $listMapper): void
    {
        $listMapper
            ->addIdentifier('id', null, [
                'header_class' => 'text-center',
                'row_align' => 'center'
            ])
            ->addIdentifier('firstname', null, [
                'header_class' => 'text-center',
                'row_align' => 'center'
            ])
            ->addIdentifier('lastname', null, [
                'header_class' => 'text-center',
                'row_align' => 'center'
            ])
            ->addIdentifier('job', null, [
                'header_class' => 'text-center',
                'row_align' => 'center'
            ])
            ->addIdentifier('email', null, [
                'header_class' => 'text-center',
                'row_align' => 'center'
            ])
            ->addIdentifier('phone', null, [
                'header_class' => 'text-center',
                'row_align' => 'center'
            ]);
    }

    protected function configureRoutes(RouteCollection $collection): void
    {
        if (!$this->isChild()) {
            $collection->clear();
            return;
        }

        $collection
            ->remove('batch')
            ->remove('export')
            ->remove('show');
    }
}
