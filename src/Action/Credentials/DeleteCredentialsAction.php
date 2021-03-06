<?php

namespace App\Action\Credentials;

use App\Action\Traits\FlashTrait;
use App\Action\Traits\RoutingTrait;
use App\Action\Traits\SecurityTrait;
use App\Entity\ValueObject\Id;
use App\Repository\CredentialsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/credentials/{id}/delete", name="app_credentials_delete")
 */
final class DeleteCredentialsAction
{
    use FlashTrait;
    use RoutingTrait;
    use SecurityTrait;

    private CredentialsRepository $credentialsRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(
        CredentialsRepository $credentialsRepository,
        EntityManagerInterface $entityManager
    )
    {
        $this->credentialsRepository = $credentialsRepository;
        $this->entityManager = $entityManager;
    }

    public function __invoke(Request $request, string $id): Response
    {
        $this->denyAccessUnlessLoggedIn();

        $credentials = $this->credentialsRepository->get(Id::fromString($id));

        $this->denyAccessUnlessGranted('DELETE', $credentials);

        $this->entityManager->remove($credentials);
        $this->entityManager->flush();

        $this->flash('success', 'flash.success.credentials.delete');

        return $this->redirectToRoute('app_credentials_list');
    }
}
