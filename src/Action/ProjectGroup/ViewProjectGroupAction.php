<?php

namespace App\Action\ProjectGroup;

use App\Action\Traits\PaginationTrait;
use App\Action\Traits\TwigTrait;
use App\Repository\ProjectGroupMemberRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="app_project_group_view")
 */
final class ViewProjectGroupAction extends AbstractProjectGroupAction
{
    use PaginationTrait;
    use TwigTrait;

    private ProjectRepository $projectRepository;
    private ProjectGroupMemberRepository $projectGroupMemberRepository;

    public function __construct(ProjectGroupMemberRepository $projectGroupMemberRepository, ProjectRepository $projectRepository)
    {
        $this->projectGroupMemberRepository = $projectGroupMemberRepository;
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(Request $request, string $slug): Response
    {
        $this->preInvoke($slug);

        $this->denyAccessUnlessGranted('GUEST', $this->projectGroup);

        $pager = $this->paginate($this->getQueryBuilder(), $request);

        return $this->renderPage('view-project-group', 'app/project_group/view.html.twig', [
            'group' => $this->projectGroup,
            'member' => $this->projectGroupMemberRepository->findOneBy(['user' => $this->getUser(), 'projectGroup' => $this->projectGroup]),
            'projects' => $pager->getCurrentPageResults(),
            'pager' => $pager
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        $this->breadcrumbs->addItem('breadcrumb.project_group.overview');
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return $this->projectRepository->createQueryBuilder('p')
            ->where('p.projectGroup = :group')
            ->setParameter('group', $this->projectGroup->getId(), 'uuid_binary')
            ->orderBy('p.createdAt', 'DESC');
    }
}
