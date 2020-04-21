<?php

namespace App\Action\Project;

use App\Action\AbstractAction;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/project/{slug}/environments", name="app_project_environments")
 */
class ProjectEnvironmentsAction extends AbstractAction
{
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
            throw $this->createNotFoundException('Project not found');
        }

        $this->denyAccessUnlessGranted('MEMBER', $project);

        return $this->renderPage('project-environments', 'app/project/environments.html.twig', [
            'project' => $project
        ]);
    }
}
