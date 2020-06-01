<?php

namespace App\Action\ProjectGroup;

use App\Action\FlashTrait;
use App\Action\RoutingTrait;
use App\Entity\ProjectGroupMember;
use App\Repository\ProjectGroupMemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/member/{id}/remove", requirements={"id": "\d+"}, name="app_project_group_member_remove")
 */
class RemoveProjectGroupMemberAction extends AbstractProjectGroupAction
{
    use FlashTrait;
    use RoutingTrait;

    private $entityManager;
    private $projectGroupMemberRepository;

    public function __construct(EntityManagerInterface $entityManager, ProjectGroupMemberRepository $projectGroupMemberRepository)
    {
        $this->entityManager = $entityManager;
        $this->projectGroupMemberRepository = $projectGroupMemberRepository;
    }

    public function __invoke(string $slug, int $id): Response
    {
        $this->preInvoke($slug, false);

        $this->denyAccessUnlessGranted('MEMBER', $this->projectGroup);

        $member = $this->projectGroupMemberRepository->find($id);

        if (!$member) {
            throw new NotFoundHttpException('Group member not found');
        }

        $user = $this->getUser();

        if ($member->getAccessLevel() === ProjectGroupMember::ACCESS_LEVEL_OWNER) {
            if ($member->getUser() !== $user) {
                throw $this->createAccessDeniedException('You cannot remove the group\'s owner.');
            }

            throw $this->createAccessDeniedException('You are the group owner, therefore you cannot leave the group. Transfer the ownership to another user first.');
        }

        $this->entityManager->remove($member);
        $this->entityManager->flush();

        if ($member->getUser() === $user) {
            $this->flash('success', 'flash.success.project_group.member.leave');
            return $this->redirectToRoute('app_home');
        }

        $this->flash('success', 'flash.success.project_group.member.remove');

        return $this->redirectToRoute('app_project_group_members', ['slug' => $this->projectGroup->getSlug()]);
    }
}
