<?php

namespace App\Action\Admin;

use App\Action\PaginationTrait;
use App\Action\SecurityTrait;
use App\Action\TwigTrait;
use App\Repository\ProjectTypeRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects/types", name="app_admin_project_type_list")
 */
class ListProjectTypesAction extends AbstractAdminAction
{
    use PaginationTrait;
    use SecurityTrait;
    use TwigTrait;

    private $projectTypeRepository;

    public function __construct(ProjectTypeRepository $projectTypeRepository)
    {
        $this->projectTypeRepository = $projectTypeRepository;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $pager = $this->paginate($this->getQueryBuilder(), $request);

        return $this->renderPage('admin-list-project-types', 'app/admin/list_project_types.html.twig', [
            'types' => $pager->getCurrentPageResults(),
            'pager' => $pager
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        parent::configureBreadcrumbs();

        $this->breadcrumbs->addItem('breadcrumb.admin.project_type.list');
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return $this->projectTypeRepository->createQueryBuilder('t')
            ->orderBy('t.name');
    }
}
