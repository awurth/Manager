<?php

namespace App\Admin;

use App\Entity\CryptographicKey;
use Doctrine\ORM\QueryBuilder;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

final class SshKeyAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'ssh-key';
    protected $classnameLabel = 'SshKey';

    public function createQuery($context = 'list')
    {
        /** @var QueryBuilder $query */
        $query = parent::createQuery();

        $alias = current($query->getRootAliases());

        $query
            ->where($alias.'.type = :type')
            ->setParameter('type', CryptographicKey::TYPE_SSH);

        return $query;
    }

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
