<?php

namespace App\Action\Admin;

use App\Action\FlashTrait;
use App\Action\RoutingTrait;
use App\Action\SecurityTrait;
use App\Repository\ProjectTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects/type/{id}/delete", requirements={"id": "\d+"}, name="app_admin_project_type_delete")
 */
class DeleteProjectTypeAction
{
    use FlashTrait;
    use RoutingTrait;
    use SecurityTrait;

    private $entityManager;
    private $projectTypeRepository;

    public function __construct(EntityManagerInterface $entityManager, ProjectTypeRepository $projectTypeRepository)
    {
        $this->entityManager = $entityManager;
        $this->projectTypeRepository = $projectTypeRepository;
    }

    public function __invoke(int $id): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $projectType = $this->projectTypeRepository->find($id);

        if (!$projectType) {
            throw new NotFoundHttpException('Project type not found');
        }

        $this->entityManager->remove($projectType);
        $this->entityManager->flush();

        $this->flash('success', 'flash.success.project.type.delete');

        return $this->redirectToRoute('app_admin_project_type_list');
    }
}
