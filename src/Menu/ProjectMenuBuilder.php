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
                'label' => $project->getName(),
                'route' => 'app_project',
                'routeParameters' => [
                    'slug' => $project->getSlug()
                ],
                'attributes' => [
                    'class' => 'header'
                ],
                'extras' => [
                    'identicon' => substr($project->getName(), 0, 1)
                ]
            ]);

        $menu
            ->addChild('Overview', [
                'label' => 'project.overview',
                'route' => 'app_project',
                'routeParameters' => [
                    'slug' => $project->getSlug()
                ],
                'extras' => [
                    'icon' => 'fas fa-home'
                ]
            ]);

        $menu
            ->addChild('Settings', [
                'label' => 'project.settings',
                'route' => 'app_home',
                'extras' => [
                    'icon' => 'fas fa-cog'
                ]
            ]);

        return $menu;
    }

    private function getProject(): Project
    {
        $params = $this->requestStack->getCurrentRequest()->attributes->get('_route_params');
        return $this->projectRepository->findOneBy(['slug' => $params['slug']]);
    }
}
