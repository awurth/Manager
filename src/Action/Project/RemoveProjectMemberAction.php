<?php

namespace App\Action\Project;

use App\Action\AbstractAction;
use App\Action\SecurityTrait;
use App\Entity\ProjectMember;
use App\Repository\ProjectMemberRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/project/{slug}/member/{id}/remove", requirements={"id": "\d+"}, name="app_project_member_remove")
 */
class RemoveProjectMemberAction extends AbstractAction
{
    use SecurityTrait;

    private $entityManager;
    private $flashBag;
    private $projectMemberRepository;
    private $projectRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        ProjectMemberRepository $projectMemberRepository,
        ProjectRepository $projectRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->projectMemberRepository = $projectMemberRepository;
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(string $slug, int $id): Response
    {
        $this->denyAccessUnlessLoggedIn();

        $project = $this->projectRepository->findOneBy(['slug' => $slug]);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        $this->denyAccessUnlessGranted('MEMBER', $project);

        $member = $this->projectMemberRepository->find($id);

        if (!$member) {
            throw $this->createNotFoundException('Project member not found');
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
        } else {
            $this->flashBag->add('success', 'flash.success.project.member.remove');
        }

        return $this->redirectToRoute('app_project_members', ['slug' => $project->getSlug()]);
    }
}
