<?php

namespace App\Action\Credentials;

use App\Action\SecurityTrait;
use App\Action\TwigTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/credentials", name="app_credentials_list")
 */
class ListCredentialsAction
{
    use SecurityTrait;
    use TwigTrait;

    public function __invoke(): Response
    {
        $this->denyAccessUnlessLoggedIn();

        return $this->renderPage('list-credentials', 'app/credentials/list.html.twig', [
            'credentials' => $this->getUser()->getCredentialsList()
        ]);
    }
}
