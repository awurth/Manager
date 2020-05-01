<?php

namespace App\Action\Admin;

use App\Action\SecurityTrait;
use App\Action\TwigTrait;
use App\Repository\ProjectGroupRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/groups", name="app_admin_project_group_list")
 */
class ListProjectGroupsAction extends AbstractAdminAction
{
    use SecurityTrait;
    use TwigTrait;

    private $projectGroupRepository;

    public function __construct(ProjectGroupRepository $projectGroupRepository)
    {
        $this->projectGroupRepository = $projectGroupRepository;
    }

    public function __invoke(): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $groups = $this->projectGroupRepository->findAll();

        return $this->renderPage('admin-list-project-groups', 'app/admin/list_project_groups.html.twig', [
            'groups' => $groups
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        parent::configureBreadcrumbs();

        $this->breadcrumbs->addItem('breadcrumb.admin.project_group.list');
    }
}
