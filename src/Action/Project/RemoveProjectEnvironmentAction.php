<?php

namespace App\Action\Project;

use App\Action\Traits\FlashTrait;
use App\Entity\ValueObject\Id;
use App\Repository\ProjectEnvironmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/environment/{id}/remove", name="app_project_environment_remove")
 */
final class RemoveProjectEnvironmentAction extends AbstractProjectAction
{
    use FlashTrait;

    private EntityManagerInterface $entityManager;
    private ProjectEnvironmentRepository $projectEnvironmentRepository;

    public function __construct(EntityManagerInterface $entityManager, ProjectEnvironmentRepository $projectEnvironmentRepository)
    {
        $this->entityManager = $entityManager;
        $this->projectEnvironmentRepository = $projectEnvironmentRepository;
    }

    public function __invoke(Request $request, string $projectGroupSlug, string $projectSlug, string $id): Response
    {
        $this->preInvoke($projectGroupSlug, $projectSlug, false);

        $this->denyAccessUnlessGranted('MEMBER', $this->project);

        $environment = $this->projectEnvironmentRepository->get(Id::fromString($id));

        if ($environment->getProject() !== $this->project) {
            throw $this->createAccessDeniedException('The environment does not belong to the current project');
        }

        $this->entityManager->remove($environment);
        $this->entityManager->flush();

        $this->flash('success', 'flash.success.project.environment.remove');

        return $this->redirectToEntity($this->project, 'environment_list');
    }
}
