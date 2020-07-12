<?php

namespace App\Action\Server;

use App\Action\Traits\EntityUrlTrait;
use App\Action\Traits\FlashTrait;
use App\Action\Traits\RoutingTrait;
use App\Entity\ServerMember;
use App\Repository\ServerMemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/member/{memberId}/remove", name="app_server_member_remove")
 */
class RemoveServerMemberAction extends AbstractServerAction
{
    use EntityUrlTrait;
    use FlashTrait;
    use RoutingTrait;

    private EntityManagerInterface $entityManager;
    private ServerMemberRepository $serverMemberRepository;

    public function __construct(EntityManagerInterface $entityManager, ServerMemberRepository $serverMemberRepository)
    {
        $this->entityManager = $entityManager;
        $this->serverMemberRepository = $serverMemberRepository;
    }

    public function __invoke(string $id, string $memberId): Response
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

            $this->flash('error', 'flash.error.server_owner_leave');
            return $this->redirectToEntity($this->server, 'view');
        }

        $this->entityManager->remove($member);
        $this->entityManager->flush();

        if ($member->getUser() === $user) {
            $this->flash('success', 'flash.success.server.member.leave');
            return $this->redirectToRoute('app_home');
        }

        $this->flash('success', 'flash.success.server.member.remove');

        return $this->redirectToRoute('app_server_members', ['id' => $this->server->getId()]);
    }
}
