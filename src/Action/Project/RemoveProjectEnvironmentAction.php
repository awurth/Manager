<?php

namespace App\Action\Project;

use App\Action\RoutingTrait;
use App\Repository\ProjectEnvironmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/environment/{id}/remove", requirements={"id": "\d+"}, name="app_project_environment_remove")
 */
class RemoveProjectEnvironmentAction extends AbstractProjectAction
{
    use RoutingTrait;

    private $entityManager;
    private $flashBag;
    private $projectEnvironmentRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        ProjectEnvironmentRepository $projectEnvironmentRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->projectEnvironmentRepository = $projectEnvironmentRepository;
    }

    public function __invoke(Request $request, string $projectGroupSlug, string $projectSlug, int $id): Response
    {
        $this->preInvoke($projectGroupSlug, $projectSlug, false);

        $environment = $this->projectEnvironmentRepository->find($id);

        if (!$environment) {
            throw new NotFoundHttpException('Project environment not found');
        }

        $this->denyAccessUnlessGranted('MEMBER', $this->project);

        $this->entityManager->remove($environment);
        $this->entityManager->flush();

        $this->flashBag->add('success', 'flash.success.project.environment.remove');

        return $this->redirectToRoute('app_project_environment_list', [
            'projectGroupSlug' => $this->projectGroup->getSlug(),
            'projectSlug' => $this->project->getSlug()
        ]);
    }
}
