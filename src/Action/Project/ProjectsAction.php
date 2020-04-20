<?php

namespace App\Action\Project;

use App\Action\AbstractAction;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects", name="app_projects")
 */
class ProjectsAction extends AbstractAction
{
    public function __invoke(): Response
    {
        $this->denyAccessUnlessLoggedIn();

        return $this->renderPage('projects', 'app/project/projects.html.twig', [
            'projects' => $this->getUser()->getProjects()
        ]);
    }
}
