<?php

namespace App\Action;

use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects", name="app_projects")
 */
class ProjectsAction extends AbstractAction
{
    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(): Response
    {
        $projects = $this->projectRepository->findAll();

        return $this->render('app/projects.html.twig', [
            'projects' => $projects
        ]);
    }
}
