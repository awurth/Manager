<?php

namespace App\Action\Project;

use App\Action\RoutingTrait;
use App\Entity\ProjectMember;
use App\Repository\ProjectMemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/group/{projectGroupSlug}/project/{projectSlug}/member/{id}/remove", requirements={"id": "\d+"}, name="app_project_member_remove")
 */
class RemoveProjectMemberAction extends AbstractProjectAction
{
    use RoutingTrait;

    private $entityManager;
    private $flashBag;
    private $projectMemberRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        ProjectMemberRepository $projectMemberRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->projectMemberRepository = $projectMemberRepository;
    }

    public function __invoke(string $projectGroupSlug, string $projectSlug, int $id): Response
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

            throw $this->createAccessDeniedException('You are the project owner, therefore you cannot leave the project. Transfer the ownership to another user first.');
        }

        $this->entityManager->remove($member);
        $this->entityManager->flush();

        if ($member->getUser() === $user) {
            $this->flashBag->add('success', 'flash.success.project.member.leave');
            return $this->redirectToRoute('app_home');
        }

        $this->flashBag->add('success', 'flash.success.project.member.remove');

        return $this->redirectToRoute('app_project_members', [
            'projectGroupSlug' => $this->projectGroup->getSlug(),
            'projectSlug' => $this->project->getSlug()
        ]);
    }
}
