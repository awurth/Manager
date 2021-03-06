<?php

namespace App\Menu;

use App\Entity\Server;
use App\Entity\ValueObject\Id;
use App\Repository\ServerRepository;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class ServerMenuBuilder
{
    private AuthorizationCheckerInterface $authorizationChecker;
    private FactoryInterface $factory;
    private ServerRepository $serverRepository;
    private RequestStack $requestStack;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        FactoryInterface $factory,
        ServerRepository $serverRepository,
        RequestStack $requestStack
    )
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->factory = $factory;
        $this->serverRepository = $serverRepository;
        $this->requestStack = $requestStack;
    }

    public function create(array $options): ItemInterface
    {
        $server = $this->getServer();
        $menu = $this->factory->createItem('Server Menu');

        $menu
            ->addChild('Server', [
                'attributes' => [
                    'class' => 'header'
                ],
                'extras' => [
                    'identicon' => substr($server->getName(), 0, 1),
                    'translation_domain' => false
                ],
                'label' => $server->getName(),
                'route' => 'app_server_view',
                'routeParameters' => [
                    'id' => $server->getId()
                ]
            ]);

        $menu
            ->addChild('Overview', [
                'extras' => [
                    'icon' => 'fas fa-home'
                ],
                'label' => 'server.overview',
                'route' => 'app_server_view',
                'routeParameters' => [
                    'id' => $server->getId()
                ]
            ]);

        $menu
            ->addChild('Users', [
                'extras' => [
                    'icon' => 'fas fa-terminal'
                ],
                'label' => 'server.users',
                'route' => 'app_home',
                /*'route' => 'app_server_users',
                'routeParameters' => [
                    'id' => $server->getId()
                ]*/
            ]);

        $menu
            ->addChild('Environments', [
                'extras' => [
                    'icon' => 'fas fa-code-branch'
                ],
                'label' => 'server.environments',
                'route' => 'app_home',
                /*'route' => 'app_server_environments',
                'routeParameters' => [
                    'id' => $server->getId()
                ]*/
            ]);

        if ($this->authorizationChecker->isGranted('MEMBER', $server)) {
            $menu
                ->addChild('Members', [
                    'extras' => [
                        'icon' => 'fas fa-users'
                    ],
                    'label' => 'server.members',
                    'route' => 'app_server_members',
                    'routeParameters' => [
                        'id' => $server->getId()
                    ]
                ]);
        }

        if ($this->authorizationChecker->isGranted('EDIT', $server)) {
            $menu
                ->addChild('Settings', [
                    'extras' => [
                        'icon' => 'fas fa-cog'
                    ],
                    'label' => 'server.settings',
                    'route' => 'app_server_edit',
                    'routeParameters' => [
                        'id' => $server->getId()
                    ]
                ]);
        }

        return $menu;
    }

    private function getServer(): Server
    {
        /**
         * @psalm-suppress PossiblyNullPropertyFetch
         * @psalm-suppress PossiblyNullReference
         */
        $params = $this->requestStack->getCurrentRequest()->attributes->get('_route_params');
        return $this->serverRepository->get(Id::fromString($params['id'] ?? ''));
    }
}
