<?php

namespace App\Action\Admin;

use App\Action\AbstractAction;
use App\Repository\CredentialsRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/credentials", name="app_admin_credentials")
 */
class CredentialsAction extends AbstractAction
{
    private $credentialsRepository;

    public function __construct(CredentialsRepository $credentialsRepository)
    {
        $this->credentialsRepository = $credentialsRepository;
    }

    public function __invoke(): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $credentials = $this->credentialsRepository->findAll();

        return $this->renderPage('admin-credentials', 'app/admin/credentials.html.twig', [
            'credentials' => $credentials
        ]);
    }
}
