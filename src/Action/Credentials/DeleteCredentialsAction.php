<?php

namespace App\Action\Credentials;

use App\Action\AbstractAction;
use App\Action\RoutingTrait;
use App\Action\SecurityTrait;
use App\Repository\CredentialsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/credentials/{id}/delete", requirements={"id": "\d+"}, name="app_credentials_delete")
 */
class DeleteCredentialsAction extends AbstractAction
{
    use RoutingTrait;
    use SecurityTrait;

    private $credentialsRepository;
    private $entityManager;
    private $flashBag;

    public function __construct(
        CredentialsRepository $credentialsRepository,
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag
    )
    {
        $this->credentialsRepository = $credentialsRepository;
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
    }

    public function __invoke(Request $request, int $id): Response
    {
        $this->denyAccessUnlessLoggedIn();

        $credentials = $this->credentialsRepository->find($id);

        if (!$credentials) {
            throw $this->createNotFoundException('Credentials not found');
        }

        $this->denyAccessUnlessGranted('DELETE', $credentials);

        $this->entityManager->remove($credentials);
        $this->entityManager->flush();

        $this->flashBag->add('success', 'flash.success.credentials.delete');

        return $this->redirectToRoute('app_credentials_list');
    }
}
