<?php

namespace App\Action\Admin;

use App\Action\PaginationTrait;
use App\Action\SecurityTrait;
use App\Action\TwigTrait;
use App\Repository\CredentialsRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/credentials", name="app_admin_credentials_list")
 */
class ListCredentialsAction extends AbstractAdminAction
{
    use PaginationTrait;
    use SecurityTrait;
    use TwigTrait;

    private $credentialsRepository;

    public function __construct(CredentialsRepository $credentialsRepository)
    {
        $this->credentialsRepository = $credentialsRepository;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $pager = $this->paginate($this->getQueryBuilder(), $request);

        return $this->renderPage('admin-list-credentials', 'app/admin/list_credentials.html.twig', [
            'credentials' => $pager->getCurrentPageResults(),
            'pager' => $pager
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        parent::configureBreadcrumbs();

        $this->breadcrumbs->addItem('breadcrumb.admin.credentials.list');
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return $this->credentialsRepository->createQueryBuilder('c')
            ->orderBy('c.name');
    }
}
