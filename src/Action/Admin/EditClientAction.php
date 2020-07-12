<?php

namespace App\Action\Admin;

use App\Action\Traits\FlashTrait;
use App\Action\Traits\RoutingTrait;
use App\Action\Traits\SecurityTrait;
use App\Action\Traits\TwigTrait;
use App\Form\Type\Action\Admin\EditClientType;
use App\Form\Model\Admin\EditClient;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/client/{id}/edit", name="app_admin_client_edit")
 */
class EditClientAction extends AbstractAdminAction
{
    use FlashTrait;
    use RoutingTrait;
    use SecurityTrait;
    use TwigTrait;

    private ClientRepository $clientRepository;
    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;

    public function __construct(
        ClientRepository $clientRepository,
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory
    )
    {
        $this->clientRepository = $clientRepository;
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }

    public function __invoke(Request $request, string $id): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $client = $this->clientRepository->find($id);

        if (!$client) {
            throw new NotFoundHttpException('Client not found');
        }

        $model = new EditClient($client);
        $form = $this->formFactory->create(EditClientType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $client->updateFromAdminEditionForm($model);

            $this->entityManager->persist($client);
            $this->entityManager->flush();

            $this->flash('success', 'flash.success.client.edit');

            return $this->redirectToRoute('app_admin_client_list');
        }

        $this->breadcrumbs->addItem($client->getName(), '', [], false);

        return $this->renderPage('admin-edit-client', 'app/admin/edit_client.html.twig', [
            'client' => $client,
            'form' => $form->createView()
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        parent::configureBreadcrumbs();

        $this->breadcrumbs->addRouteItem('breadcrumb.admin.client.list', 'app_admin_client_list');
    }
}
