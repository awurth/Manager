<?php

namespace App\Menu;

use App\Entity\User;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\Security\Core\Security;

class ProfileMenuBuilder
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

        $menu = $this->factory->createItem('Profile Menu');

        $menu
            ->addChild('Profile', [
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
            ->addChild('General', [
                'extras' => [
                    'icon' => 'fas fa-user-circle'
                ],
                'label' => 'profile.general',
                'route' => 'app_profile'
            ]);

        return $menu;
    }
}
