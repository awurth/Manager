<?php

namespace App\Action\Credentials;

use App\Action\AbstractAction;
use App\Repository\CredentialsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/credentials/{id}", requirements={"id": "\d+"}, name="app_credentials_view")
 */
class ViewCredentialsAction extends AbstractAction
{
    private $credentialsRepository;

    public function __construct(CredentialsRepository $credentialsRepository)
    {
        $this->credentialsRepository = $credentialsRepository;
    }

    public function __invoke(int $id): Response
    {
        $this->denyAccessUnlessLoggedIn();

        $credentials = $this->credentialsRepository->find($id);

        if (!$credentials) {
            throw $this->createNotFoundException('Credentials not found');
        }

        $this->denyAccessUnlessGranted('VIEW', $credentials);

        return $this->renderPage('view-credentials', 'app/credentials/view.html.twig', [
            'credentials' => $credentials
        ]);
    }
}
