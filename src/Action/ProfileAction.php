<?php

namespace App\Action;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile", name="app_profile")
 */
class ProfileAction extends AbstractAction
{
    use SecurityTrait;

    public function __invoke(): Response
    {
        $this->denyAccessUnlessLoggedIn();

        return $this->renderPage('profile', 'app/profile.html.twig');
    }
}
