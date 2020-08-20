<?php

namespace App\Action\Project;

use App\Action\Traits\BreadcrumbsTrait;
use App\Action\Traits\EntityUrlTrait;
use App\Action\Traits\SecurityTrait;
use App\Entity\Project;
use App\Entity\ProjectGroup;
use App\Repository\ProjectGroupRepository;
use App\Repository\ProjectRepository;

abstract class AbstractProjectAction
{
    use BreadcrumbsTrait;
    use EntityUrlTrait;
    use SecurityTrait;

    protected ProjectGroupRepository $projectGroupRepository;
    protected ProjectRepository $projectRepository;

    protected ProjectGroup $projectGroup;
    protected Project $project;

    protected function preInvoke(string $projectGroupSlug, string $projectSlug, bool $breadcrumb = true): void
    {
        $this->denyAccessUnlessLoggedIn();

        $this->projectGroup = $this->projectGroupRepository->getBySlug($projectGroupSlug);
        $this->project = $this->projectRepository->getBySlug($projectSlug);

        if ($breadcrumb) {
            $this->breadcrumbs->prependItem(
                $this->project->getName(),
                $this->entityUrlGenerator->generate($this->project, 'view'),
                [],
                false
            );

            if ($this->security->isGranted('VIEW', $this->projectGroup)) {
                $this->breadcrumbs->prependItem(
                    $this->projectGroup->getName(),
                    $this->entityUrlGenerator->generate($this->projectGroup, 'view'),
                    [],
                    false
                );
            } else {
                $this->breadcrumbs->prependItem($this->projectGroup->getName(), '', [], false);
            }
        }
    }

    /**
     * @required
     */
    public function setProjectGroupRepository(ProjectGroupRepository $projectGroupRepository): void
    {
        $this->projectGroupRepository = $projectGroupRepository;
    }

    /**
     * @required
     */
    public function setProjectRepository(ProjectRepository $projectRepository): void
    {
        $this->projectRepository = $projectRepository;
    }
}
