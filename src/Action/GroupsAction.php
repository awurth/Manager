<?php

namespace App\Action;

use App\Repository\ProjectGroupRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/groups", name="app_groups")
 */
class GroupsAction extends AbstractAction
{
    private $projectGroupRepository;

    public function __construct(ProjectGroupRepository $projectGroupRepository)
    {
        $this->projectGroupRepository = $projectGroupRepository;
    }

    public function __invoke(): Response
    {
        $this->denyAccessUnlessLoggedIn();

        $groups = $this->projectGroupRepository->findAll();

        return $this->renderPage('groups', 'app/groups.html.twig', [
            'groups' => $groups
        ]);
    }
}
