<?php

namespace App\Menu;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;

class AdminMenuBuilder
{
    private $factory;

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
                    'icon' => 'fas fa-wrench',
                    'translation_domain' => false
                ],
                'label' => 'Administration',
                'route' => 'app_admin'
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
                    'icon' => 'fas fa-tasks'
                ],
                'label' => 'admin.projects',
                'route' => 'app_admin_project_list'
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
            ->addChild('Customers', [
                'extras' => [
                    'icon' => 'fas fa-user-tie'
                ],
                'label' => 'admin.customers',
                'route' => 'app_admin_customer_list'
            ]);

        return $menu;
    }
}
