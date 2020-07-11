<?php

namespace App\Action\ProjectGroup;

use App\Action\Traits\BreadcrumbsTrait;
use App\Action\Traits\SecurityTrait;
use App\Entity\ProjectGroup;
use App\Repository\ProjectGroupRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractProjectGroupAction
{
    use BreadcrumbsTrait;
    use SecurityTrait;

    /**
     * @var ProjectGroupRepository
     */
    protected $projectGroupRepository;

    /**
     * @var ProjectGroup
     */
    protected $projectGroup;

    protected function preInvoke(string $slug, bool $breadcrumb = true): void
    {
        $this->denyAccessUnlessLoggedIn();

        $this->projectGroup = $this->projectGroupRepository->findOneBy(['slug' => $slug]);

        if (!$this->projectGroup) {
            throw new NotFoundHttpException('Project group not found');
        }

        if ($breadcrumb) {
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
}
