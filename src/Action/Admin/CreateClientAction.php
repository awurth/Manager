<?php

namespace App\Action\Admin;

use App\Action\Traits\FlashTrait;
use App\Action\Traits\RoutingTrait;
use App\Action\Traits\SecurityTrait;
use App\Action\Traits\TwigTrait;
use App\Entity\Client;
use App\Form\Type\Action\Admin\CreateClientType;
use App\Form\Model\Admin\CreateClient;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/clients/new", name="app_admin_client_create")
 */
class CreateClientAction extends AbstractAdminAction
{
    use FlashTrait;
    use RoutingTrait;
    use SecurityTrait;
    use TwigTrait;

    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $model = new CreateClient();
        $form = $this->formFactory->create(CreateClientType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client = Client::createFromAdminCreationForm($model);

            $this->entityManager->persist($client);
            $this->entityManager->flush();

            $this->flash('success', 'flash.success.client.create');

            return $this->redirectToRoute('app_admin_client_list');
        }

        return $this->renderPage('admin-create-client', 'app/admin/create_client.html.twig', [
            'form' => $form->createView()
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        parent::configureBreadcrumbs();

        $this->breadcrumbs
            ->addRouteItem('breadcrumb.admin.client.list', 'app_admin_client_list')
            ->addItem('breadcrumb.admin.client.create');
    }
}
