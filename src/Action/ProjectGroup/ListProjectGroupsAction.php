<?php

namespace App\Action\ProjectGroup;

use App\Action\AbstractAction;
use App\Action\SecurityTrait;
use App\Action\TwigTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/groups", name="app_group_list")
 */
class ListProjectGroupsAction extends AbstractAction
{
    use SecurityTrait;
    use TwigTrait;

    public function __invoke(): Response
    {
        $this->denyAccessUnlessLoggedIn();

        return $this->renderPage('list-project-groups', 'app/project_group/list.html.twig', [
            'groups' => $this->getUser()->getProjectGroups()
        ]);
    }
}
