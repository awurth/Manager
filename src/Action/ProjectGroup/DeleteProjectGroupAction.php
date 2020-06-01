<?php

namespace App\Action\ProjectGroup;

use App\Action\FlashTrait;
use App\Action\RoutingTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/delete", name="app_project_group_delete")
 */
class DeleteProjectGroupAction extends AbstractProjectGroupAction
{
    use FlashTrait;
    use RoutingTrait;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(string $slug): Response
    {
        $this->preInvoke($slug, false);

        $this->denyAccessUnlessGranted('DELETE', $this->projectGroup);

        $this->entityManager->remove($this->projectGroup);
        $this->entityManager->flush();

        $this->flash('success', 'flash.success.project_group.delete');

        return $this->redirectToRoute('app_project_group_list');
    }
}
