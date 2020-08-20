<?php

namespace App\Action\Project;

use App\Action\Traits\EntityUrlTrait;
use App\Action\Traits\FlashTrait;
use App\Action\Traits\RoutingTrait;
use App\Entity\ProjectMember;
use App\Repository\ProjectMemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/member/{id}/remove", name="app_project_member_remove")
 */
final class RemoveProjectMemberAction extends AbstractProjectAction
{
    use FlashTrait;
    use RoutingTrait;

    private EntityManagerInterface $entityManager;
    private ProjectMemberRepository $projectMemberRepository;

    public function __construct(EntityManagerInterface $entityManager, ProjectMemberRepository $projectMemberRepository)
    {
        $this->entityManager = $entityManager;
        $this->projectMemberRepository = $projectMemberRepository;
    }

    public function __invoke(string $projectGroupSlug, string $projectSlug, string $id): Response
    {
        $this->preInvoke($projectGroupSlug, $projectSlug, false);

        $this->denyAccessUnlessGranted('MEMBER', $this->project);

        $member = $this->projectMemberRepository->find($id);

        if (!$member) {
            throw new NotFoundHttpException('Project member not found');
        }

        $user = $this->getUser();

        if ($member->getAccessLevel() === ProjectMember::ACCESS_LEVEL_OWNER) {
            if ($member->getUser() !== $user) {
                throw $this->createAccessDeniedException('You cannot remove the project\'s owner.');
            }

            $this->flash('error', 'flash.error.project_owner_leave');
            return $this->redirectToEntity($this->project, 'view');
        }

        $this->entityManager->remove($member);
        $this->entityManager->flush();

        if ($member->getUser() === $user) {
            $this->flash('success', 'flash.success.project.member.leave');
            return $this->redirectToRoute('app_home');
        }

        $this->flash('success', 'flash.success.project.member.remove');

        return $this->redirectToEntity($this->project, 'members');
    }
}
