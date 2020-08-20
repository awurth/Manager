<?php

namespace App\Action\Admin;

use App\Action\Traits\SecurityTrait;
use App\Action\Traits\TwigTrait;
use App\Repository\ClientRepository;
use App\Repository\ProjectGroupRepository;
use App\Repository\ProjectRepository;
use App\Repository\ServerRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="app_admin")
 */
final class AdminAction extends AbstractAdminAction
{
    use SecurityTrait;
    use TwigTrait;

    private ClientRepository $clientRepository;
    private ProjectGroupRepository $projectGroupRepository;
    private ProjectRepository $projectRepository;
    private ServerRepository $serverRepository;

    public function __construct(
        ClientRepository $clientRepository,
        ProjectGroupRepository $projectGroupRepository,
        ProjectRepository $projectRepository,
        ServerRepository $serverRepository
    )
    {
        $this->clientRepository = $clientRepository;
        $this->projectGroupRepository = $projectGroupRepository;
        $this->projectRepository = $projectRepository;
        $this->serverRepository = $serverRepository;
    }

    public function __invoke(): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $groupsCount = $this->projectGroupRepository->count([]);
        $projectsCount = $this->projectRepository->count([]);
        $serversCount = $this->serverRepository->count([]);
        $clientsCount = $this->clientRepository->count([]);

        return $this->renderPage('admin', 'app/admin/admin.html.twig', [
            'groupsCount' => $groupsCount,
            'projectsCount' => $projectsCount,
            'serversCount' => $serversCount,
            'clientsCount' => $clientsCount
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        parent::configureBreadcrumbs();

        $this->breadcrumbs->addItem('breadcrumb.admin.dashboard');
    }
}
