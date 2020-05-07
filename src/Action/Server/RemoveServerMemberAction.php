<?php

namespace App\Action\Server;

use App\Action\RoutingTrait;
use App\Entity\ServerMember;
use App\Repository\ServerMemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/member/{memberId}/remove", requirements={"member": "\d+"}, name="app_server_member_remove")
 */
class RemoveServerMemberAction extends AbstractServerAction
{
    use RoutingTrait;

    private $entityManager;
    private $flashBag;
    private $serverMemberRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        ServerMemberRepository $serverMemberRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->serverMemberRepository = $serverMemberRepository;
    }

    public function __invoke(int $id, int $memberId): Response
    {
        $this->preInvoke($id, false);

        $this->denyAccessUnlessGranted('MEMBER', $this->server);

        $member = $this->serverMemberRepository->find($memberId);

        if (!$member) {
            throw new NotFoundHttpException('Server member not found');
        }

        $user = $this->getUser();

        if ($member->getAccessLevel() === ServerMember::ACCESS_LEVEL_OWNER) {
            if ($member->getUser() !== $user) {
                throw $this->createAccessDeniedException('You cannot remove the server\'s owner.');
            }

            throw $this->createAccessDeniedException('You are the server owner, therefore you cannot leave the server. Transfer the ownership to another user first.');
        }

        $this->entityManager->remove($member);
        $this->entityManager->flush();

        if ($member->getUser() === $user) {
            $this->flashBag->add('success', 'flash.success.server.member.leave');
            return $this->redirectToRoute('app_home');
        }

        $this->flashBag->add('success', 'flash.success.server.member.remove');

        return $this->redirectToRoute('app_server_members', ['id' => $this->server->getId()]);
    }
}
