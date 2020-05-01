<?php

namespace App\Action\Admin;

use App\Action\SecurityTrait;
use App\Action\TwigTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="app_admin")
 */
class AdminAction extends AbstractAdminAction
{
    use SecurityTrait;
    use TwigTrait;

    public function __invoke(): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->renderPage('admin', 'app/admin/admin.html.twig');
    }

    protected function configureBreadcrumbs(): void
    {
        parent::configureBreadcrumbs();

        $this->breadcrumbs->addItem('breadcrumb.admin.dashboard');
    }
}
