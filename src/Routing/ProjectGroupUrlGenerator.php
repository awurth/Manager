<?php

namespace App\Routing;

use App\Entity\ProjectGroup;

final class ProjectGroupUrlGenerator extends AbstractEntityUrlGenerator
{
    public function generate($entity, string $action, array $parameters = []): string
    {
        /** @var ProjectGroup $entity */

        $parameters['slug'] = $entity->getSlug();

        return $this->router->generate('app_project_group_'.$action, $parameters);
    }

    public function supports($entity): bool
    {
        return $entity instanceof ProjectGroup;
    }
}
