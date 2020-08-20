<?php

namespace App\Routing;

use App\Entity\Project;

final class ProjectUrlGenerator extends AbstractEntityUrlGenerator
{
    /**
     * @param Project $project
     * @param string  $action
     * @param array   $parameters
     *
     * @return string
     */
    public function generate($project, string $action, array $parameters = []): string
    {
        $parameters['projectGroupSlug'] = $project->getProjectGroup()->getSlug();
        $parameters['projectSlug'] = $project->getSlug();

        return $this->router->generate('app_project_'.$action, $parameters);
    }

    public function supports($entity): bool
    {
        return $entity instanceof Project;
    }
}
