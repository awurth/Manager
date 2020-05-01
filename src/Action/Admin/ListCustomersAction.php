<?php

namespace App\Action\Admin;

use App\Action\AbstractAction;
use App\Action\SecurityTrait;
use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/customers", name="app_admin_customer_list")
 */
class ListCustomersAction extends AbstractAction
{
    use SecurityTrait;

    private $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function __invoke(): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $customers = $this->customerRepository->findAll();

        return $this->renderPage('admin-list-customers', 'app/admin/list_customers.html.twig', [
            'customers' => $customers
        ]);
    }
}
