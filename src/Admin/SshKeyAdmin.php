<?php

namespace App\Admin;

use App\Entity\CryptographicKey;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\ProxyQueryInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQuery;

final class SshKeyAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'ssh-key';
    protected $classnameLabel = 'SshKey';

    protected function configureBatchActions($actions): array
    {
        return [];
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('name')
            ->add('value', null, ['label' => 'form.label_ssh_key_value']);
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
            ->addIdentifier('createdAt', null, [
                'header_class' => 'text-center',
                'row_align' => 'center'
            ]);
    }

    protected function configureQuery(ProxyQueryInterface $query): ProxyQueryInterface
    {
        /** @var ProxyQuery $query */
        $qb = $query->getQueryBuilder();
        $alias = current($qb->getRootAliases());

        $query
            ->where($alias.'.type = :type')
            ->setParameter('type', CryptographicKey::TYPE_SSH);

        return $query;
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
