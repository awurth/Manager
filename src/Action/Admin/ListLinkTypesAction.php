<?php

namespace App\Action\Admin;

use App\Action\Traits\PaginationTrait;
use App\Action\Traits\SecurityTrait;
use App\Action\Traits\TwigTrait;
use App\Repository\LinkTypeRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/link-types", name="app_admin_link_type_list")
 */
class ListLinkTypesAction extends AbstractAdminAction
{
    use PaginationTrait;
    use SecurityTrait;
    use TwigTrait;

    private $linkTypeRepository;

    public function __construct(LinkTypeRepository $linkTypeRepository)
    {
        $this->linkTypeRepository = $linkTypeRepository;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $pager = $this->paginate($this->getQueryBuilder(), $request);

        return $this->renderPage('admin-list-link-types', 'app/admin/list_link_types.html.twig', [
            'types' => $pager->getCurrentPageResults(),
            'pager' => $pager
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        parent::configureBreadcrumbs();

        $this->breadcrumbs->addItem('breadcrumb.admin.link_type.list');
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return $this->linkTypeRepository->createQueryBuilder('l')
            ->orderBy('l.name');
    }
}
