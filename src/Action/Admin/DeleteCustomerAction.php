<?php

namespace App\Action\Admin;

use App\Action\RoutingTrait;
use App\Action\SecurityTrait;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/customer/{id}/delete", requirements={"id": "\d+"}, name="app_admin_customer_delete")
 */
class DeleteCustomerAction
{
    use RoutingTrait;
    use SecurityTrait;

    private $entityManager;
    private $flashBag;
    private $customerRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        CustomerRepository $customerRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->customerRepository = $customerRepository;
    }

    public function __invoke(int $id): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $customer = $this->customerRepository->find($id);

        if (!$customer) {
            throw new NotFoundHttpException('Customer not found');
        }

        $this->entityManager->remove($customer);
        $this->entityManager->flush();

        $this->flashBag->add('success', 'flash.success.customer.delete');

        return $this->redirectToRoute('app_admin_customer_list');
    }
}
