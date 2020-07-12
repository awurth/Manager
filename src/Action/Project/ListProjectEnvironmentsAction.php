<?php

namespace App\Action\Project;

use App\Action\Traits\TwigTrait;
use App\Repository\ProjectEnvironmentRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/environments", name="app_project_environment_list")
 */
class ListProjectEnvironmentsAction extends AbstractProjectAction
{
    use TwigTrait;

    private ProjectEnvironmentRepository $projectEnvironmentRepository;

    public function __construct(ProjectEnvironmentRepository $projectEnvironmentRepository)
    {
        $this->projectEnvironmentRepository = $projectEnvironmentRepository;
    }

    public function __invoke(Request $request, string $projectGroupSlug, string $projectSlug): Response
    {
        $this->preInvoke($projectGroupSlug, $projectSlug);

        $this->denyAccessUnlessGranted('MEMBER', $this->project);

        $environments = $this->projectEnvironmentRepository->findBy(['project' => $this->project]);

        return $this->renderPage('list-project-environments', 'app/project/list_environments.html.twig', [
            'project' => $this->project,
            'environments' => $environments
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        $this->breadcrumbs->addItem('breadcrumb.project.environment.list');
    }
}
