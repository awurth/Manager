<?php

namespace App\Action\Project;

use App\Action\RoutingTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/group/{projectGroupSlug}/project/{projectSlug}/delete", name="app_project_delete")
 */
class DeleteProjectAction extends AbstractProjectAction
{
    use RoutingTrait;

    private $entityManager;
    private $flashBag;

    public function __construct(EntityManagerInterface $entityManager, FlashBagInterface $flashBag)
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
    }

    public function __invoke(Request $request, string $projectGroupSlug, string $projectSlug): Response
    {
        $this->preInvoke($projectGroupSlug, $projectSlug, false);

        $this->denyAccessUnlessGranted('DELETE', $this->project);

        $this->entityManager->remove($this->project);
        $this->entityManager->flush();

        $this->flashBag->add('success', 'flash.success.project.delete');

        return $this->redirectToRoute('app_project_list');
    }
}
