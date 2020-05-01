<?php

namespace App\Action\Project;

use App\Action\SecurityTrait;
use App\Action\TwigTrait;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/project/{slug}", name="app_project_view")
 */
class ViewProjectAction
{
    use SecurityTrait;
    use TwigTrait;

    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(string $slug): Response
    {
        $this->denyAccessUnlessLoggedIn();

        $project = $this->projectRepository->findOneBy(['slug' => $slug]);

        if (!$project) {
            throw new NotFoundHttpException('Project not found');
        }

        $this->denyAccessUnlessGranted('GUEST', $project);

        return $this->renderPage('view-project', 'app/project/view.html.twig', [
            'project' => $project
        ]);
    }
}
