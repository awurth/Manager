<?php

namespace App\Menu;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class ProjectMenuBuilder
{
    private $authorizationChecker;
    private $factory;
    private $projectRepository;
    private $requestStack;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        FactoryInterface $factory,
        ProjectRepository $projectRepository,
        RequestStack $requestStack
    )
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->factory = $factory;
        $this->projectRepository = $projectRepository;
        $this->requestStack = $requestStack;
    }

    public function create(array $options): ItemInterface
    {
        $project = $this->getProject();
        $menu = $this->factory->createItem('Project Menu');

        $menu
            ->addChild('Project', [
                'attributes' => [
                    'class' => 'header'
                ],
                'extras' => [
                    'identicon' => substr($project->getName(), 0, 1),
                    'translation_domain' => false
                ],
                'label' => $project->getName(),
                'route' => 'app_project',
                'routeParameters' => [
                    'slug' => $project->getSlug()
                ]
            ]);

        $menu
            ->addChild('Overview', [
                'extras' => [
                    'icon' => 'fas fa-home'
                ],
                'label' => 'project.overview',
                'route' => 'app_project',
                'routeParameters' => [
                    'slug' => $project->getSlug()
                ]
            ]);

        $menu
            ->addChild('Environments', [
                'extras' => [
                    'icon' => 'fas fa-server',
                    'routes' => [
                        'app_project_environment_create',
                        'app_project_environment_edit'
                    ]
                ],
                'label' => 'project.environments',
                'route' => 'app_project_environments',
                'routeParameters' => [
                    'slug' => $project->getSlug()
                ]
            ]);

        $menu
            ->addChild('Members', [
                'extras' => [
                    'icon' => 'fas fa-users'
                ],
                'label' => 'project.members',
                'route' => 'app_project_members',
                'routeParameters' => [
                    'slug' => $project->getSlug()
                ]
            ]);

        if ($this->authorizationChecker->isGranted('EDIT', $project)) {
            $menu
                ->addChild('Settings', [
                    'extras' => [
                        'icon' => 'fas fa-cog'
                    ],
                    'label' => 'project.settings',
                    'route' => 'app_project_edit',
                    'routeParameters' => [
                        'slug' => $project->getSlug()
                    ]
                ]);
        }

        return $menu;
    }

    private function getProject(): Project
    {
        $params = $this->requestStack->getCurrentRequest()->attributes->get('_route_params');
        return $this->projectRepository->findOneBy(['slug' => $params['slug']]);
    }
}
