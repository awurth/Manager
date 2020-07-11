<?php

namespace App\Action\Server;

use App\Action\TwigTrait;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="app_server_view")
 */
class ViewServerAction extends AbstractServerAction
{
    use TwigTrait;

    public function __invoke(string $id): Response
    {
        $this->preInvoke($id);

        $this->denyAccessUnlessGranted('GUEST', $this->server);

        return $this->renderPage('view-server', 'app/server/view.html.twig', [
            'server' => $this->server
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        $this->breadcrumbs->addItem('breadcrumb.server.overview');
    }
}
