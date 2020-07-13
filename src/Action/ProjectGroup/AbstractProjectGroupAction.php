<?php

namespace App\Action\ProjectGroup;

use App\Action\Traits\BreadcrumbsTrait;
use App\Action\Traits\EntityUrlTrait;
use App\Action\Traits\SecurityTrait;
use App\Entity\ProjectGroup;
use App\Repository\ProjectGroupRepository;

abstract class AbstractProjectGroupAction
{
    use BreadcrumbsTrait;
    use EntityUrlTrait;
    use SecurityTrait;

    protected ProjectGroupRepository $projectGroupRepository;

    protected ProjectGroup $projectGroup;

    protected function preInvoke(string $slug, bool $breadcrumb = true): void
    {
        $this->denyAccessUnlessLoggedIn();

        $this->projectGroup = $this->projectGroupRepository->getBySlug($slug);

        if ($breadcrumb) {
            $this->breadcrumbs->prependItem(
                $this->projectGroup->getName(),
                $this->entityUrlGenerator->generate($this->projectGroup, 'view'),
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
