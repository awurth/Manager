<?php

namespace App\Action;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/profile", name="app_profile")
 */
class ProfileAction extends AbstractAction
{
    public function __invoke(): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app_login');
        }

        return $this->renderPage('profile', 'app/profile.html.twig');
    }
}
