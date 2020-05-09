<?php

namespace App\Action\ProjectGroup;

use App\Action\RoutingTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/delete", name="app_project_group_delete")
 */
class DeleteProjectGroupAction extends AbstractProjectGroupAction
{
    use RoutingTrait;

    private $entityManager;
    private $flashBag;

    public function __construct(EntityManagerInterface $entityManager, FlashBagInterface $flashBag)
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
    }

    public function __invoke(string $slug): Response
    {
        $this->preInvoke($slug, false);

        $this->denyAccessUnlessGranted('DELETE', $this->projectGroup);

        $this->entityManager->remove($this->projectGroup);
        $this->entityManager->flush();

        $this->flashBag->add('success', 'flash.success.project_group.delete');

        return $this->redirectToRoute('app_project_group_list');
    }
}
