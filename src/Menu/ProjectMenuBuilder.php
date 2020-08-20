<?php

namespace App\Menu;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Awurth\UploadBundle\Storage\StorageInterface;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

final class ProjectMenuBuilder
{
    private AuthorizationCheckerInterface $authorizationChecker;
    private FactoryInterface $factory;
    private ProjectRepository $projectRepository;
    private RequestStack $requestStack;
    private StorageInterface $uploader;

    public function __construct(
        AuthorizationCheckerInterface $authorizationChecker,
        FactoryInterface $factory,
        ProjectRepository $projectRepository,
        RequestStack $requestStack,
        StorageInterface $uploader
    )
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->factory = $factory;
        $this->projectRepository = $projectRepository;
        $this->requestStack = $requestStack;
        $this->uploader = $uploader;
    }

    public function create(array $options): ItemInterface
    {
        $project = $this->getProject();
        $menu = $this->factory->createItem('Project Menu');

        $headerExtras = ['translation_domain' => false];
        if ($project->getLogoFilename()) {
            $headerExtras['image'] = $this->uploader->resolveUri($project, 'project_logo');
        } else {
            $headerExtras['identicon'] = substr($project->getName(), 0, 1);
        }

        $menu
            ->addChild('Project', [
                'attributes' => [
                    'class' => 'header'
                ],
                'extras' => $headerExtras,
                'label' => $project->getName(),
                'route' => 'app_project_view',
                'routeParameters' => [
                    'projectGroupSlug' => $project->getProjectGroup()->getSlug(),
                    'projectSlug' => $project->getSlug()
                ]
            ]);

        $menu
            ->addChild('Overview', [
                'extras' => [
                    'icon' => 'fas fa-home'
                ],
                'label' => 'project.overview',
                'route' => 'app_project_view',
                'routeParameters' => [
                    'projectGroupSlug' => $project->getProjectGroup()->getSlug(),
                    'projectSlug' => $project->getSlug()
                ]
            ]);

        $menu
            ->addChild('Environments', [
                'extras' => [
                    'icon' => 'fas fa-code-branch',
                    'routes' => [
                        'app_project_environment_add',
                        'app_project_environment_edit'
                    ]
                ],
                'label' => 'project.environments',
                'route' => 'app_project_environment_list',
                'routeParameters' => [
                    'projectGroupSlug' => $project->getProjectGroup()->getSlug(),
                    'projectSlug' => $project->getSlug()
                ]
            ]);

        if ($this->authorizationChecker->isGranted('MEMBER', $project)) {
            $menu
                ->addChild('Members', [
                    'extras' => [
                        'icon' => 'fas fa-users'
                    ],
                    'label' => 'project.members',
                    'route' => 'app_project_members',
                    'routeParameters' => [
                        'projectGroupSlug' => $project->getProjectGroup()->getSlug(),
                        'projectSlug' => $project->getSlug()
                    ]
                ]);
        }

        if ($this->authorizationChecker->isGranted('EDIT', $project)) {
            $menu
                ->addChild('Settings', [
                    'extras' => [
                        'icon' => 'fas fa-cog'
                    ],
                    'label' => 'project.settings.settings',
                    'route' => 'app_project_edit',
                    'routeParameters' => [
                        'projectGroupSlug' => $project->getProjectGroup()->getSlug(),
                        'projectSlug' => $project->getSlug()
                    ]
                ]);

            $menu['Settings']->addChild('General', [
                'label' => 'project.settings.general',
                'route' => 'app_project_edit',
                'routeParameters' => [
                    'projectGroupSlug' => $project->getProjectGroup()->getSlug(),
                    'projectSlug' => $project->getSlug()
                ]
            ]);

            $menu['Settings']->addChild('Links', [
                'label' => 'project.settings.links',
                'route' => 'app_project_link_list',
                'routeParameters' => [
                    'projectGroupSlug' => $project->getProjectGroup()->getSlug(),
                    'projectSlug' => $project->getSlug()
                ]
            ]);
        }

        return $menu;
    }

    private function getProject(): Project
    {
        /**
         * @psalm-suppress PossiblyNullPropertyFetch
         * @psalm-suppress PossiblyNullReference
         */
        $params = $this->requestStack->getCurrentRequest()->attributes->get('_route_params');
        return $this->projectRepository->getBySlug($params['projectSlug'] ?? '');
    }
}
