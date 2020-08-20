<?php

namespace App\Action\Admin;

use App\Action\Traits\FlashTrait;
use App\Action\Traits\RoutingTrait;
use App\Action\Traits\SecurityTrait;
use App\Entity\ValueObject\Id;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/client/{id}/delete", name="app_admin_client_delete")
 */
final class DeleteClientAction
{
    use FlashTrait;
    use RoutingTrait;
    use SecurityTrait;

    private ClientRepository $clientRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(ClientRepository $clientRepository, EntityManagerInterface $entityManager)
    {
        $this->clientRepository = $clientRepository;
        $this->entityManager = $entityManager;
    }

    public function __invoke(string $id): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $client = $this->clientRepository->get(Id::fromString($id));

        $this->entityManager->remove($client);
        $this->entityManager->flush();

        $this->flash('success', 'flash.success.client.delete');

        return $this->redirectToRoute('app_admin_client_list');
    }
}
