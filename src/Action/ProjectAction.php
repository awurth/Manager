<?php

namespace App\Action;

use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/project/{id}", name="app_project")
 */
class ProjectAction extends AbstractAction
{
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(int $id): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app_login');
        }

        $project = $this->projectRepository->find($id);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        return $this->renderPage('project', 'app/project.html.twig', [
            'project' => $project
        ]);
    }
}
