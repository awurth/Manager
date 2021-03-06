<?php

namespace App\Action\Server;

use App\Action\Traits\FlashTrait;
use App\Action\Traits\RoutingTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/delete", name="app_server_delete")
 */
final class DeleteServerAction extends AbstractServerAction
{
    use FlashTrait;
    use RoutingTrait;

    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(string $id): Response
    {
        $this->preInvoke($id, false);

        $this->denyAccessUnlessGranted('DELETE', $this->server);

        $this->entityManager->remove($this->server);
        $this->entityManager->flush();

        $this->flash('success', 'flash.success.server.delete');

        return $this->redirectToRoute('app_server_list');
    }
}
