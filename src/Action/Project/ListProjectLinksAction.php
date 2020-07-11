<?php

namespace App\Action\Project;

use App\Action\Traits\PaginationTrait;
use App\Action\Traits\TwigTrait;
use App\Repository\LinkRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/links", name="app_project_link_list")
 */
class ListProjectLinksAction extends AbstractProjectAction
{
    use PaginationTrait;
    use TwigTrait;

    private $linkRepository;

    public function __construct(LinkRepository $linkRepository)
    {
        $this->linkRepository = $linkRepository;
    }

    public function __invoke(Request $request, string $projectGroupSlug, string $projectSlug): Response
    {
        $this->preInvoke($projectGroupSlug, $projectSlug);

        $this->denyAccessUnlessGranted('MEMBER', $this->project);

        $pager = $this->paginate($this->getQueryBuilder(), $request);

        return $this->renderPage('list-project-links', 'app/project/list_links.html.twig', [
            'project' => $this->project,
            'links' => $pager->getCurrentPageResults(),
            'pager' => $pager
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        $this->breadcrumbs->addItem('breadcrumb.project.link.list');
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return $this->linkRepository->createQueryBuilder('l')
            ->leftJoin('l.linkType', 't')->addSelect('t')
            ->where('l.project = :project')
            ->setParameter('project', $this->project->getId(), 'uuid_binary')
            ->orderBy('t.name')
            ->addOrderBy('l.uri');
    }
}
