<?php

namespace App\Action\Admin;

use App\Action\AbstractAction;
use App\Action\SecurityTrait;
use App\Action\TwigTrait;
use App\Form\Type\EditCustomerType;
use App\Form\Model\EditCustomer;
use App\Repository\CustomerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/customers/{id}/edit", requirements={"id": "\d+"}, name="app_admin_customer_edit")
 */
class EditCustomerAction extends AbstractAction
{
    use SecurityTrait;
    use TwigTrait;

    private $customerRepository;
    private $entityManager;
    private $flashBag;
    private $formFactory;

    public function __construct(
        CustomerRepository $customerRepository,
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        FormFactoryInterface $formFactory
    )
    {
        $this->customerRepository = $customerRepository;
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->formFactory = $formFactory;
    }

    public function __invoke(Request $request, int $id): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $customer = $this->customerRepository->find($id);

        if (!$customer) {
            throw $this->createNotFoundException('Customer not found');
        }

        $model = new EditCustomer($customer);
        $form = $this->formFactory->create(EditCustomerType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $customer
                ->setName($model->name)
                ->setAddress($model->address)
                ->setPostcode($model->postcode)
                ->setCity($model->city)
                ->setPhone($model->phone)
                ->setEmail($model->email);

            $this->entityManager->persist($customer);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'flash.success.customer.edit');

            return $this->redirectToRoute('app_admin_customer_list');
        }

        return $this->renderPage('admin-edit-customer', 'app/admin/edit_customer.html.twig', [
            'customer' => $customer,
            'form' => $form->createView()
        ]);
    }
}
