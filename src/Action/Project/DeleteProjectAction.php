<?php

namespace App\Action\Project;

use App\Action\FlashTrait;
use App\Action\RoutingTrait;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/delete", name="app_project_delete")
 */
class DeleteProjectAction extends AbstractProjectAction
{
    use FlashTrait;
    use RoutingTrait;

    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function __invoke(Request $request, string $projectGroupSlug, string $projectSlug): Response
    {
        $this->preInvoke($projectGroupSlug, $projectSlug, false);

        $this->denyAccessUnlessGranted('DELETE', $this->project);

        $this->entityManager->remove($this->project);
        $this->entityManager->flush();

        $this->flash('success', 'flash.success.project.delete');

        return $this->redirectToRoute('app_project_list');
    }
}
