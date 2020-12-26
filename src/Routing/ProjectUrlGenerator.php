<?php

namespace App\Routing;

use App\Entity\Project;

final class ProjectUrlGenerator extends AbstractEntityUrlGenerator
{
    public function generate($entity, string $action, array $parameters = []): string
    {
        /** @var Project $entity */

        $parameters['projectGroupSlug'] = $entity->getProjectGroup()->getSlug();
        $parameters['projectSlug'] = $entity->getSlug();

        return $this->router->generate('app_project_'.$action, $parameters);
    }

    public function supports($entity): bool
    {
        return $entity instanceof Project;
    }
}
