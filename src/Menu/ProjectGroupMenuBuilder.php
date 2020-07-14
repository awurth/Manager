<?php

namespace App\Menu;

use App\Entity\ProjectGroup;
use App\Repository\ProjectGroupRepository;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProjectGroupMenuBuilder
{
    private AuthorizationCheckerInterface $authorizationChecker;
    private FactoryInterface $factory;
    private ProjectGroupRepository $projectGroupRepository;
    private RequestStack $requestStack;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        FactoryInterface $factory,
        ProjectGroupRepository $projectGroupRepository,
        RequestStack $requestStack
    )
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->factory = $factory;
        $this->projectGroupRepository = $projectGroupRepository;
        $this->requestStack = $requestStack;
    }

    public function create(array $options): ItemInterface
    {
        $group = $this->getProjectGroup();
        $menu = $this->factory->createItem('Project Group Menu');

        $menu
            ->addChild('ProjectGroup', [
                'attributes' => [
                    'class' => 'header'
                ],
                'extras' => [
                    'identicon' => substr($group->getName(), 0, 1),
                    'translation_domain' => false
                ],
                'label' => $group->getName(),
                'route' => 'app_project_group_view',
                'routeParameters' => [
                    'slug' => $group->getSlug()
                ]
            ]);

        $menu
            ->addChild('Overview', [
                'extras' => [
                    'icon' => 'fas fa-home'
                ],
                'label' => 'project_group.overview',
                'route' => 'app_project_group_view',
                'routeParameters' => [
                    'slug' => $group->getSlug()
                ]
            ]);

        if ($this->authorizationChecker->isGranted('MEMBER', $group)) {
            $menu
                ->addChild('Members', [
                    'extras' => [
                        'icon' => 'fas fa-users'
                    ],
                    'label' => 'project_group.members',
                    'route' => 'app_project_group_members',
                    'routeParameters' => [
                        'slug' => $group->getSlug()
                    ]
                ]);
        }

        if ($this->authorizationChecker->isGranted('EDIT', $group)) {
            $menu
                ->addChild('Settings', [
                    'extras' => [
                        'icon' => 'fas fa-cog'
                    ],
                    'label' => 'project_group.settings',
                    'route' => 'app_project_group_edit',
                    'routeParameters' => [
                        'slug' => $group->getSlug()
                    ]
                ]);
        }

        return $menu;
    }

    private function getProjectGroup(): ProjectGroup
    {
        /**
         * @psalm-suppress PossiblyNullPropertyFetch
         * @psalm-suppress PossiblyNullReference
         */
        $params = $this->requestStack->getCurrentRequest()->attributes->get('_route_params');
        return $this->projectGroupRepository->getBySlug($params['slug'] ?? '');
    }
}
