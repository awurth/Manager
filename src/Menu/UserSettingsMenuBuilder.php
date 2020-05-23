<?php

namespace App\Menu;

use App\Entity\User;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Security;

class UserSettingsMenuBuilder
{
    private $factory;
    private $security;

    public function __construct(FactoryInterface $factory, Security $security)
    {
        $this->factory = $factory;
        $this->security = $security;
    }

    public function create(array $options): ItemInterface
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $menu = $this->factory->createItem('User Settings Menu');

        $menu
            ->addChild('User Settings', [
                'attributes' => [
                    'class' => 'header'
                ],
                'extras' => [
                    'identicon' => substr($user->getFirstname(), 0, 1).substr($user->getLastname(), 0, 1),
                    'translation_domain' => false
                ],
                'label' => $user->getFullName(),
                'route' => 'app_profile'
            ]);

        $menu
            ->addChild('Profile', [
                'extras' => [
                    'icon' => 'fas fa-user-circle'
                ],
                'label' => 'user_settings.profile',
                'route' => 'app_profile'
            ]);

        return $menu;
    }
}
