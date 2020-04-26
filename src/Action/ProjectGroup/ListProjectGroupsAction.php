<?php

namespace App\Action\ProjectGroup;

use App\Action\AbstractAction;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/groups", name="app_groups")
 */
class ListProjectGroupsAction extends AbstractAction
{
    public function __invoke(): Response
    {
        $this->denyAccessUnlessLoggedIn();

        return $this->renderPage('list-project-groups', 'app/project_group/list.html.twig', [
            'groups' => $this->getUser()->getProjectGroups()
        ]);
    }
}
