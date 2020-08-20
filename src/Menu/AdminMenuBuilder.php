<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

final class AdminMenuBuilder
{
    private FactoryInterface $factory;

    public function __construct(FactoryInterface $factory)
    {
        $this->factory = $factory;
    }

    public function create(array $options): ItemInterface
    {
        $menu = $this->factory->createItem('Admin Menu');

        $menu
            ->addChild('Admin', [
                'attributes' => [
                    'class' => 'header'
                ],
                'extras' => [
                    'icon' => 'fas fa-tools',
                    'translation_domain' => false
                ],
                'label' => 'Administration',
                'route' => 'app_admin'
            ]);

        $menu
            ->addChild('Project Groups', [
                'extras' => [
                    'icon' => 'fas fa-object-group'
                ],
                'label' => 'admin.project_groups',
                'route' => 'app_admin_project_group_list'
            ]);

        $menu
            ->addChild('Projects', [
                'extras' => [
                    'icon' => 'fas fa-code'
                ],
                'label' => 'admin.projects',
                'route' => 'app_admin_project_list'
            ]);

        $menu
            ->addChild('Servers', [
                'extras' => [
                    'icon' => 'fas fa-server'
                ],
                'label' => 'admin.servers',
                'route' => 'app_admin_server_list'
            ]);

        $menu
            ->addChild('Credentials', [
                'extras' => [
                    'icon' => 'fas fa-key'
                ],
                'label' => 'admin.credentials',
                'route' => 'app_admin_credentials_list'
            ]);

        $menu
            ->addChild('Clients', [
                'extras' => [
                    'icon' => 'fas fa-user-tie',
                    'routes' => [
                        'app_admin_client_create',
                        'app_admin_client_edit'
                    ]
                ],
                'label' => 'admin.clients',
                'route' => 'app_admin_client_list'
            ]);

        $menu
            ->addChild('Users', [
                'extras' => [
                    'icon' => 'fas fa-users',
                    'routes' => [
                        'app_admin_user_create'
                    ]
                ],
                'label' => 'admin.users',
                'route' => 'app_admin_user_list'
            ]);

        $menu
            ->addChild('Link Types', [
                'extras' => [
                    'icon' => 'fas fa-link',
                    'routes' => [
                        'app_admin_link_type_create'
                    ]
                ],
                'label' => 'admin.link_types',
                'route' => 'app_admin_link_type_list'
            ]);

        return $menu;
    }
}
