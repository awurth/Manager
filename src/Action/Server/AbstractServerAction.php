<?php

namespace App\Action\Server;

use App\Action\Traits\BreadcrumbsTrait;
use App\Action\Traits\SecurityTrait;
use App\Entity\Server;
use App\Entity\ValueObject\Id;
use App\Repository\ServerRepository;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractServerAction
{
    use BreadcrumbsTrait;
    use SecurityTrait;

    protected ServerRepository $serverRepository;

    protected Server $server;

    protected function preInvoke(string $id, bool $breadcrumb = true): void
    {
        $this->denyAccessUnlessLoggedIn();

        $this->server = $this->serverRepository->get(Id::fromString($id));

        if ($breadcrumb) {
            $this->breadcrumbs->prependRouteItem(
                $this->server->getName(),
                'app_server_view',
                ['id' => $this->server->getId()],
                RouterInterface::ABSOLUTE_PATH,
                [],
                false
            );

            $this->breadcrumbs->prependRouteItem('breadcrumb.server.list', 'app_server_list');
        }
    }

    /**
     * @required
     */
    public function setServerRepository(ServerRepository $serverRepository): void
    {
        $this->serverRepository = $serverRepository;
    }
}
