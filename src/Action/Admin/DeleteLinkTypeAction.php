<?php

namespace App\Action\Admin;

use App\Action\FlashTrait;
use App\Action\RoutingTrait;
use App\Action\SecurityTrait;
use App\Repository\LinkTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/link-type/{id}/delete", name="app_admin_link_type_delete")
 */
class DeleteLinkTypeAction
{
    use FlashTrait;
    use RoutingTrait;
    use SecurityTrait;

    private $entityManager;
    private $linkTypeRepository;

    public function __construct(EntityManagerInterface $entityManager, LinkTypeRepository $linkTypeRepository)
    {
        $this->entityManager = $entityManager;
        $this->linkTypeRepository = $linkTypeRepository;
    }

    public function __invoke(string $id): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $linkType = $this->linkTypeRepository->find($id);

        if (!$linkType) {
            throw new NotFoundHttpException('Link type not found');
        }

        $this->entityManager->remove($linkType);
        $this->entityManager->flush();

        $this->flash('success', 'flash.success.link_type.delete');

        return $this->redirectToRoute('app_admin_link_type_list');
    }
}
