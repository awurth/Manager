<?php

namespace App\Action\Credentials;

use App\Action\AbstractAction;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/credentials", name="app_credentials_list")
 */
class CredentialsAction extends AbstractAction
{
    public function __invoke(): Response
    {
        $this->denyAccessUnlessLoggedIn();

        return $this->renderPage('credentials', 'app/credentials/list.html.twig', [
            'credentials' => $this->getUser()->getCredentialsList()
        ]);
    }
}
