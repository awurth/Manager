<?php

namespace App\Action\ProjectGroup;

use App\Action\AbstractAction;
use App\Action\RoutingTrait;
use App\Action\SecurityTrait;
use App\Entity\ProjectGroupMember;
use App\Repository\ProjectGroupMemberRepository;
use App\Repository\ProjectGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/group/{slug}/member/{id}/remove", requirements={"id": "\d+"}, name="app_project_group_member_remove")
 */
class RemoveProjectGroupMemberAction extends AbstractAction
{
    use RoutingTrait;
    use SecurityTrait;

    private $entityManager;
    private $flashBag;
    private $projectGroupMemberRepository;
    private $projectGroupRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        ProjectGroupMemberRepository $projectGroupMemberRepository,
        ProjectGroupRepository $projectGroupRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->projectGroupMemberRepository = $projectGroupMemberRepository;
        $this->projectGroupRepository = $projectGroupRepository;
    }

    public function __invoke(string $slug, int $id): Response
    {
        $this->denyAccessUnlessLoggedIn();

        $group = $this->projectGroupRepository->findOneBy(['slug' => $slug]);

        if (!$group) {
            throw $this->createNotFoundException('Group not found');
        }

        $this->denyAccessUnlessGranted('MEMBER', $group);

        $member = $this->projectGroupMemberRepository->find($id);

        if (!$member) {
            throw $this->createNotFoundException('Group member not found');
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
            $this->flashBag->add('success', 'flash.success.project_group.member.leave');
        } else {
            $this->flashBag->add('success', 'flash.success.project_group.member.remove');
        }

        return $this->redirectToRoute('app_project_group_members', ['slug' => $group->getSlug()]);
    }
}
