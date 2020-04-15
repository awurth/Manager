<?php

namespace App\Menu;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class ProjectMenuBuilder
{
    private $factory;
    private $projectRepository;
    private $requestStack;

    public function __construct(FactoryInterface $factory, ProjectRepository $projectRepository, RequestStack $requestStack)
    {
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
            ->addChild('Settings', [
                'extras' => [
                    'icon' => 'fas fa-cog'
                ],
                'label' => 'project.settings',
                'route' => 'app_home'
            ]);

        return $menu;
    }

    private function getProject(): Project
    {
        $params = $this->requestStack->getCurrentRequest()->attributes->get('_route_params');
        return $this->projectRepository->findOneBy(['slug' => $params['slug']]);
    }
}
