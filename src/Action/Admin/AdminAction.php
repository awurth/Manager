<?php

namespace App\Action\Admin;

use App\Action\AbstractAction;
use App\Action\SecurityTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="app_admin")
 */
class AdminAction extends AbstractAction
{
    use SecurityTrait;

    public function __invoke(): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->renderPage('admin', 'app/admin/admin.html.twig');
    }
}
