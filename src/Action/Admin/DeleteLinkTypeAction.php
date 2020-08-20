<?php

namespace App\Action\Admin;

use App\Action\Traits\FlashTrait;
use App\Action\Traits\RoutingTrait;
use App\Action\Traits\SecurityTrait;
use App\Entity\ValueObject\Id;
use App\Repository\LinkTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/link-type/{id}/delete", name="app_admin_link_type_delete")
 */
final class DeleteLinkTypeAction
{
    use FlashTrait;
    use RoutingTrait;
    use SecurityTrait;

    private EntityManagerInterface $entityManager;
    private LinkTypeRepository $linkTypeRepository;

    public function __construct(EntityManagerInterface $entityManager, LinkTypeRepository $linkTypeRepository)
    {
        $this->entityManager = $entityManager;
        $this->linkTypeRepository = $linkTypeRepository;
    }

    public function __invoke(string $id): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $linkType = $this->linkTypeRepository->get(Id::fromString($id));

        $this->entityManager->remove($linkType);
        $this->entityManager->flush();

        $this->flash('success', 'flash.success.link_type.delete');

        return $this->redirectToRoute('app_admin_link_type_list');
    }
}
