<?php

namespace App\Action\Admin;

use App\Action\Traits\PaginationTrait;
use App\Action\Traits\SecurityTrait;
use App\Action\Traits\TwigTrait;
use App\Repository\ServerRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/servers", name="app_admin_server_list")
 */
class ListServersAction extends AbstractAdminAction
{
    use PaginationTrait;
    use SecurityTrait;
    use TwigTrait;

    private ServerRepository $serverRepository;

    public function __construct(ServerRepository $serverRepository)
    {
        $this->serverRepository = $serverRepository;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $pager = $this->paginate($this->getQueryBuilder(), $request);

        return $this->renderPage('admin-list-servers', 'app/admin/list_servers.html.twig', [
            'servers' => $pager->getCurrentPageResults(),
            'pager' => $pager
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        parent::configureBreadcrumbs();

        $this->breadcrumbs->addItem('breadcrumb.admin.server.list');
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return $this->serverRepository->createQueryBuilder('s')
            ->orderBy('s.createdAt', 'DESC');
    }
}
