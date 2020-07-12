<?php

namespace App\Action\Project;

use App\Action\Traits\FlashTrait;
use App\Action\Traits\RoutingTrait;
use App\Repository\LinkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/link/{id}/remove", name="app_project_link_remove")
 */
class RemoveProjectLinkAction extends AbstractProjectAction
{
    use FlashTrait;
    use RoutingTrait;

    private EntityManagerInterface $entityManager;
    private LinkRepository $linkRepository;

    public function __construct(EntityManagerInterface $entityManager, LinkRepository $linkRepository)
    {
        $this->entityManager = $entityManager;
        $this->linkRepository = $linkRepository;
    }

    public function __invoke(Request $request, string $projectGroupSlug, string $projectSlug, string $id): Response
    {
        $this->preInvoke($projectGroupSlug, $projectSlug, false);

        $this->denyAccessUnlessGranted('MEMBER', $this->project);

        $link = $this->linkRepository->find($id);

        if (!$link) {
            throw new NotFoundHttpException('Project link not found');
        }

        if ($link->getProject() !== $this->project) {
            throw $this->createAccessDeniedException('The link does not belong to the current project');
        }

        $this->entityManager->remove($link);
        $this->entityManager->flush();

        $this->flash('success', 'flash.success.project.link.remove');

        return $this->redirectToRoute('app_project_link_list', [
            'projectGroupSlug' => $this->projectGroup->getSlug(),
            'projectSlug' => $this->project->getSlug()
        ]);
    }
}
