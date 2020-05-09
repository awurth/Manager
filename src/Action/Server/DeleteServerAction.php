<?php

namespace App\Action\Server;

use App\Action\RoutingTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/delete", name="app_server_delete")
 */
class DeleteServerAction extends AbstractServerAction
{
    use RoutingTrait;

    private $entityManager;
    private $flashBag;

    public function __construct(EntityManagerInterface $entityManager, FlashBagInterface $flashBag)
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
    }

    public function __invoke(int $id): Response
    {
        $this->preInvoke($id, false);

        $this->denyAccessUnlessGranted('DELETE', $this->server);

        $this->entityManager->remove($this->server);
        $this->entityManager->flush();

        $this->flashBag->add('success', 'flash.success.server.delete');

        return $this->redirectToRoute('app_server_list');
    }
}
