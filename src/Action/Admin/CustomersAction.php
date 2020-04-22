<?php

namespace App\Action\Admin;

use App\Action\AbstractAction;
use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/customers", name="app_admin_customers")
 */
class CustomersAction extends AbstractAction
{
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

        return $this->renderPage('admin-customers', 'app/admin/customers.html.twig', [
            'customers' => $customers
        ]);
    }
}
