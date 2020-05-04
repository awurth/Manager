<?php

namespace App\Action\Admin;

use App\Action\PaginationTrait;
use App\Action\SecurityTrait;
use App\Action\TwigTrait;
use App\Repository\CustomerRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/customers", name="app_admin_customer_list")
 */
class ListCustomersAction extends AbstractAdminAction
{
    use PaginationTrait;
    use SecurityTrait;
    use TwigTrait;

    private $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $pager = $this->paginate($this->getQueryBuilder(), $request);

        return $this->renderPage('admin-list-customers', 'app/admin/list_customers.html.twig', [
            'customers' => $pager->getCurrentPageResults(),
            'pager' => $pager
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        parent::configureBreadcrumbs();

        $this->breadcrumbs->addItem('breadcrumb.admin.customer.list');
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return $this->customerRepository->createQueryBuilder('c')
            ->orderBy('c.createdAt', 'DESC');
    }
}
