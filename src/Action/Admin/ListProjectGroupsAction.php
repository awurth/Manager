<?php

namespace App\Action\Admin;

use App\Action\AbstractAction;
use App\Action\SecurityTrait;
use App\Repository\ProjectGroupRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/groups", name="app_admin_project_group_list")
 */
class ListProjectGroupsAction extends AbstractAction
{
    use SecurityTrait;

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
}
