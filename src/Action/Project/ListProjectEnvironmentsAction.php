<?php

namespace App\Action\Project;

use App\Action\SecurityTrait;
use App\Action\TwigTrait;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/project/{slug}/environments", name="app_project_environment_list")
 */
class ListProjectEnvironmentsAction
{
    use SecurityTrait;
    use TwigTrait;

    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(Request $request, string $slug): Response
    {
        $this->denyAccessUnlessLoggedIn();

        $project = $this->projectRepository->findOneBy(['slug' => $slug]);

        if (!$project) {
            throw new NotFoundHttpException('Project not found');
        }

        $this->denyAccessUnlessGranted('MEMBER', $project);

        return $this->renderPage('list-project-environments', 'app/project/list_environments.html.twig', [
            'project' => $project
        ]);
    }
}
