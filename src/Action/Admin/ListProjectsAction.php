<?php

namespace App\Action\Admin;

use App\Action\Traits\PaginationTrait;
use App\Action\Traits\SecurityTrait;
use App\Action\Traits\TwigTrait;
use App\Repository\ProjectRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects", name="app_admin_project_list")
 */
class ListProjectsAction extends AbstractAdminAction
{
    use PaginationTrait;
    use SecurityTrait;
    use TwigTrait;

    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $pager = $this->paginate($this->getQueryBuilder(), $request);

        return $this->renderPage('admin-list-projects', 'app/admin/list_projects.html.twig', [
            'projects' => $pager->getCurrentPageResults(),
            'pager' => $pager
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        parent::configureBreadcrumbs();

        $this->breadcrumbs->addItem('breadcrumb.admin.project.list');
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return $this->projectRepository->createQueryBuilder('p')
            ->orderBy('p.createdAt', 'DESC');
    }
}
