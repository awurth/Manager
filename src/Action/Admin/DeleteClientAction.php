<?php

namespace App\Action\Admin;

use App\Action\FlashTrait;
use App\Action\RoutingTrait;
use App\Action\SecurityTrait;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/client/{id}/delete", requirements={"id": "\d+"}, name="app_admin_client_delete")
 */
class DeleteClientAction
{
    use FlashTrait;
    use RoutingTrait;
    use SecurityTrait;

    private $entityManager;
    private $clientRepository;

    public function __construct(EntityManagerInterface $entityManager, ClientRepository $clientRepository)
    {
        $this->entityManager = $entityManager;
        $this->clientRepository = $clientRepository;
    }

    public function __invoke(int $id): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $client = $this->clientRepository->find($id);

        if (!$client) {
            throw new NotFoundHttpException('Client not found');
        }

        $this->entityManager->remove($client);
        $this->entityManager->flush();

        $this->flash('success', 'flash.success.client.delete');

        return $this->redirectToRoute('app_admin_client_list');
    }
}
