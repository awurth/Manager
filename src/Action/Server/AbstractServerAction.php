<?php

namespace App\Action\Server;

use App\Action\BreadcrumbsTrait;
use App\Action\SecurityTrait;
use App\Entity\Server;
use App\Repository\ServerRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;

abstract class AbstractServerAction
{
    use BreadcrumbsTrait;
    use SecurityTrait;

    /**
     * @var ServerRepository
     */
    protected $serverRepository;

    /**
     * @var Server
     */
    protected $server;

    protected function preInvoke(string $id, bool $breadcrumb = true): void
    {
        $this->denyAccessUnlessLoggedIn();

        $this->server = $this->serverRepository->findOneBy(['id' => $id]);

        if (!$this->server) {
            throw new NotFoundHttpException('Server not found');
        }

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
