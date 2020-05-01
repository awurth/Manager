<?php

namespace App\Action\ProjectGroup;

use App\Action\AbstractAction;
use App\Action\SecurityTrait;
use App\Repository\ProjectGroupRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/group/{slug}", name="app_project_group_view")
 */
class ViewProjectGroupAction extends AbstractAction
{
    use SecurityTrait;

    private $projectGroupRepository;

    public function __construct(ProjectGroupRepository $projectGroupRepository)
    {
        $this->projectGroupRepository = $projectGroupRepository;
    }

    public function __invoke(string $slug): Response
    {
        $this->denyAccessUnlessLoggedIn();

        $group = $this->projectGroupRepository->findOneBy(['slug' => $slug]);

        if (!$group) {
            throw $this->createNotFoundException('Project group not found');
        }

        $this->denyAccessUnlessGranted('GUEST', $group);

        return $this->renderPage('view-project-group', 'app/project_group/view.html.twig', [
            'group' => $group
        ]);
    }
}
