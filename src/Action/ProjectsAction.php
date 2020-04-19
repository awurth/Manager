<?php

namespace App\Action;

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

        return $this->renderPage('projects', 'app/projects.html.twig', [
            'projects' => $this->getUser()->getProjects()
        ]);
    }
}
