<?php

namespace App\Action\ProjectGroup;

use App\Action\Traits\FlashTrait;
use App\Action\Traits\RoutingTrait;
use App\Entity\ProjectGroupMember;
use App\Entity\ValueObject\Id;
use App\Repository\ProjectGroupMemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/member/{id}/remove", name="app_project_group_member_remove")
 */
final class RemoveProjectGroupMemberAction extends AbstractProjectGroupAction
{
    use FlashTrait;
    use RoutingTrait;

    private EntityManagerInterface $entityManager;
    private ProjectGroupMemberRepository $projectGroupMemberRepository;

    public function __construct(EntityManagerInterface $entityManager, ProjectGroupMemberRepository $projectGroupMemberRepository)
    {
        $this->entityManager = $entityManager;
        $this->projectGroupMemberRepository = $projectGroupMemberRepository;
    }

    public function __invoke(string $slug, string $id): Response
    {
        $this->preInvoke($slug, false);

        $this->denyAccessUnlessGranted('MEMBER', $this->projectGroup);

        $member = $this->projectGroupMemberRepository->get(Id::fromString($id));

        $user = $this->security->getUser();

        if ($member->getAccessLevel() === ProjectGroupMember::ACCESS_LEVEL_OWNER) {
            if ($member->getUser() !== $user) {
                throw $this->createAccessDeniedException('You cannot remove the group\'s owner.');
            }

            $this->flash('error', 'flash.error.project_group_owner_leave');
            return $this->redirectToEntity($this->projectGroup, 'view');
        }

        $this->entityManager->remove($member);
        $this->entityManager->flush();

        if ($member->getUser() === $user) {
            $this->flash('success', 'flash.success.project_group.member.leave');
            return $this->redirectToRoute('app_home');
        }

        $this->flash('success', 'flash.success.project_group.member.remove');

        return $this->redirectToEntity($this->projectGroup, 'members');
    }
}
