<?php

namespace App\Action\Project;

use App\Action\Traits\FlashTrait;
use App\Action\Traits\RoutingTrait;
use App\Repository\ProjectEnvironmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/environment/{id}/remove", name="app_project_environment_remove")
 */
class RemoveProjectEnvironmentAction extends AbstractProjectAction
{
    use FlashTrait;
    use RoutingTrait;

    private $entityManager;
    private $projectEnvironmentRepository;

    public function __construct(EntityManagerInterface $entityManager, ProjectEnvironmentRepository $projectEnvironmentRepository)
    {
        $this->entityManager = $entityManager;
        $this->projectEnvironmentRepository = $projectEnvironmentRepository;
    }

    public function __invoke(Request $request, string $projectGroupSlug, string $projectSlug, string $id): Response
    {
        $this->preInvoke($projectGroupSlug, $projectSlug, false);

        $this->denyAccessUnlessGranted('MEMBER', $this->project);

        $environment = $this->projectEnvironmentRepository->find($id);

        if (!$environment) {
            throw new NotFoundHttpException('Project environment not found');
        }

        if ($environment->getProject() !== $this->project) {
            throw $this->createAccessDeniedException('The environment does not belong to the current project');
        }

        $this->entityManager->remove($environment);
        $this->entityManager->flush();

        $this->flash('success', 'flash.success.project.environment.remove');

        return $this->redirectToRoute('app_project_environment_list', [
            'projectGroupSlug' => $this->projectGroup->getSlug(),
            'projectSlug' => $this->project->getSlug()
        ]);
    }
}
