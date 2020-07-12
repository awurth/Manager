<?php

namespace App\Action\Project;

use App\Action\Traits\TwigTrait;
use App\Repository\LinkRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="app_project_view")
 */
class ViewProjectAction extends AbstractProjectAction
{
    use TwigTrait;

    private LinkRepository $linkRepository;

    public function __construct(LinkRepository $linkRepository)
    {
        $this->linkRepository = $linkRepository;
    }

    public function __invoke(string $projectGroupSlug, string $projectSlug): Response
    {
        $this->preInvoke($projectGroupSlug, $projectSlug);

        $this->denyAccessUnlessGranted('GUEST', $this->project);

        $links = $this->linkRepository->findBy(['project' => $this->project]);

        return $this->renderPage('view-project', 'app/project/view.html.twig', [
            'project' => $this->project,
            'member' => $this->project->getMemberByUser($this->getUser()),
            'links' => $links
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        $this->breadcrumbs->addItem('breadcrumb.project.overview');
    }
}
