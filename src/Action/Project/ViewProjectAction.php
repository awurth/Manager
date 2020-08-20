<?php

namespace App\Action\Project;

use App\Action\Traits\TwigTrait;
use App\Repository\LinkRepository;
use App\Repository\ProjectMemberRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="app_project_view")
 */
final class ViewProjectAction extends AbstractProjectAction
{
    use TwigTrait;

    private LinkRepository $linkRepository;
    private ProjectMemberRepository $projectMemberRepository;

    public function __construct(LinkRepository $linkRepository, ProjectMemberRepository $projectMemberRepository)
    {
        $this->linkRepository = $linkRepository;
        $this->projectMemberRepository = $projectMemberRepository;
    }

    public function __invoke(string $projectGroupSlug, string $projectSlug): Response
    {
        $this->preInvoke($projectGroupSlug, $projectSlug);

        $this->denyAccessUnlessGranted('GUEST', $this->project);

        $links = $this->linkRepository->findBy(['project' => $this->project]);

        return $this->renderPage('view-project', 'app/project/view.html.twig', [
            'project' => $this->project,
            'member' => $this->projectMemberRepository->findOneBy(['user' => $this->getUser(), 'project' => $this->project]),
            'links' => $links
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        $this->breadcrumbs->addItem('breadcrumb.project.overview');
    }
}
