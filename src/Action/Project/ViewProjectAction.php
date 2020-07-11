<?php

namespace App\Action\Project;

use App\Action\Traits\TwigTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="app_project_view")
 */
class ViewProjectAction extends AbstractProjectAction
{
    use TwigTrait;

    public function __invoke(string $projectGroupSlug, string $projectSlug): Response
    {
        $this->preInvoke($projectGroupSlug, $projectSlug);

        $this->denyAccessUnlessGranted('GUEST', $this->project);

        return $this->renderPage('view-project', 'app/project/view.html.twig', [
            'project' => $this->project,
            'member' => $this->project->getMemberByUser($this->getUser())
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        $this->breadcrumbs->addItem('breadcrumb.project.overview');
    }
}
