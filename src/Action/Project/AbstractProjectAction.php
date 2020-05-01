<?php

namespace App\Action\Project;

use App\Action\BreadcrumbsTrait;
use App\Action\SecurityTrait;
use App\Entity\Project;
use App\Entity\ProjectGroup;
use App\Repository\ProjectGroupRepository;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractProjectAction
{
    use BreadcrumbsTrait;
    use SecurityTrait;

    /**
     * @var ProjectGroupRepository
     */
    protected $projectGroupRepository;

    /**
     * @var ProjectRepository
     */
    protected $projectRepository;

    /**
     * @var ProjectGroup
     */
    protected $projectGroup;

    /**
     * @var Project
     */
    protected $project;

    protected function preInvoke(string $projectGroupSlug, string $projectSlug, bool $breadcrumb = true): void
    {
        $this->denyAccessUnlessLoggedIn();

        $this->projectGroup = $this->projectGroupRepository->findOneBy(['slug' => $projectGroupSlug]);

        if (!$this->projectGroup) {
            throw new NotFoundHttpException('Project group not found');
        }

        $this->project = $this->projectRepository->findOneBy(['slug' => $projectSlug]);

        if (!$this->project) {
            throw new NotFoundHttpException('Project not found');
        }

        if ($breadcrumb) {
            $this->breadcrumbs->prependRouteItem(
                $this->project->getName(),
                'app_project_view',
                [
                    'projectGroupSlug' => $this->projectGroup->getSlug(),
                    'projectSlug' => $this->project->getSlug()
                ],
                RouterInterface::ABSOLUTE_PATH,
                [],
                false
            );

            $this->breadcrumbs->prependRouteItem(
                $this->projectGroup->getName(),
                'app_project_group_view',
                ['slug' => $this->projectGroup->getSlug()],
                RouterInterface::ABSOLUTE_PATH,
                [],
                false
            );
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
