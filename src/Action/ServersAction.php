<?php

namespace App\Action;

use App\Repository\ProjectRepository;
use App\Repository\ServerRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/servers", name="app_servers")
 */
class ServersAction extends AbstractAction
{
    private $serverRepository;

    public function __construct(ServerRepository $serverRepository)
    {
        $this->serverRepository = $serverRepository;
    }

    public function __invoke(): Response
    {
        if (!$this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('app_login');
        }

        $servers = $this->serverRepository->findAll();

        return $this->renderPage('servers', 'app/servers.html.twig', [
            'servers' => $servers
        ]);
    }
}