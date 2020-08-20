<?php

namespace App\Routing;

use App\Entity\ProjectGroup;

final class ProjectGroupUrlGenerator extends AbstractEntityUrlGenerator
{
    /**
     * @param ProjectGroup $projectGroup
     * @param string       $action
     * @param array        $parameters
     *
     * @return string
     */
    public function generate($projectGroup, string $action, array $parameters = []): string
    {
        $parameters['slug'] = $projectGroup->getSlug();

        return $this->router->generate('app_project_group_'.$action, $parameters);
    }

    public function supports($entity): bool
    {
        return $entity instanceof ProjectGroup;
    }
}
