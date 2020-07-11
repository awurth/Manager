<?php

namespace App\Action\Credentials;

use App\Action\FlashTrait;
use App\Action\RoutingTrait;
use App\Action\SecurityTrait;
use App\Repository\CredentialsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/credentials/{id}/delete", name="app_credentials_delete")
 */
class DeleteCredentialsAction
{
    use FlashTrait;
    use RoutingTrait;
    use SecurityTrait;

    private $credentialsRepository;
    private $entityManager;

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

        $credentials = $this->credentialsRepository->find($id);

        if (!$credentials) {
            throw new NotFoundHttpException('Credentials not found');
        }

        $this->denyAccessUnlessGranted('DELETE', $credentials);

        $this->entityManager->remove($credentials);
        $this->entityManager->flush();

        $this->flash('success', 'flash.success.credentials.delete');

        return $this->redirectToRoute('app_credentials_list');
    }
}
