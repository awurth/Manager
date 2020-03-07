<?php

namespace App\Admin;

use App\Entity\Project;
use App\Entity\Server;
use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollection;

final class ProjectEnvironmentAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'project-environment';

    protected function configureBatchActions($actions): array
    {
        return [];
    }

    protected function configureFormFields(FormMapper $formMapper): void
    {
        $formMapper
            ->add('project')
            ->add('environment')
            ->add('server')
            ->add('path')
            ->add('url')
            ->add('description');

        if ($this->hasParentFieldDescription()) {
            if ($this->getParentFieldDescription()->getAssociationMapping()['sourceEntity'] === Project::class) {
                $formMapper->remove('project');
            }

            if ($this->getParentFieldDescription()->getAssociationMapping()['sourceEntity'] === Server::class) {
                $formMapper->remove('server');
            }
        }
    }

    protected function configureRoutes(RouteCollection $collection): void
    {
        $collection->clearExcept(['create', 'edit']);
    }
}
