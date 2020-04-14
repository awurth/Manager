<?php

namespace App\Action;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin", name="app_admin")
 */
class AdminAction extends AbstractAction
{
    public function __invoke(): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app_login');
        }

        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->renderPage('admin', 'app/admin/admin.html.twig');
    }
}
