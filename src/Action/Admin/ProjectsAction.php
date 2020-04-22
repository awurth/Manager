<?php

namespace App\Action\Admin;

use App\Action\AbstractAction;
use App\Repository\ProjectRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects", name="app_admin_projects")
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
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $projects = $this->projectRepository->findAll();

        return $this->renderPage('admin-projects', 'app/admin/projects.html.twig', [
            'projects' => $projects
        ]);
    }
}
