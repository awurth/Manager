<?php

namespace App\Action;

use App\Action\Traits\SecurityTrait;
use App\Action\Traits\TwigTrait;
use App\Repository\ProjectGroupRepository;
use App\Repository\ProjectRepository;
use App\Repository\ServerRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/search", name="app_search")
 */
class SearchAction
{
    use SecurityTrait;
    use TwigTrait;

    private ProjectGroupRepository $projectGroupRepository;
    private ProjectRepository $projectRepository;
    private ServerRepository $serverRepository;

    public function __construct(
        ProjectGroupRepository $projectGroupRepository,
        ProjectRepository $projectRepository,
        ServerRepository $serverRepository
    )
    {
        $this->projectGroupRepository = $projectGroupRepository;
        $this->projectRepository = $projectRepository;
        $this->serverRepository = $serverRepository;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessLoggedIn();

        $search = $request->query->get('search');

        $groups = $projects = $servers = [];
        if (strlen($search) > 2) {
            $groups = $this->findProjectGroups($search);
            $projects = $this->findProjects($search);
            $servers = $this->findServers($search);
        }

        return $this->renderPage('search', 'app/search.html.twig', [
            'groups' => $groups,
            'projects' => $projects,
            'servers' => $servers
        ]);
    }

    private function findProjectGroups(string $search): array
    {
        return $this->projectGroupRepository->createQueryBuilder('g')
            ->where('g.name LIKE :search')
            ->orWhere('g.description LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->getQuery()->getResult();
    }

    private function findProjects(string $search): array
    {
        return $this->projectRepository->createQueryBuilder('p')
            ->where('p.name LIKE :search')
            ->orWhere('p.description LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->getQuery()->getResult();
    }

    private function findServers(string $search): array
    {
        return $this->serverRepository->createQueryBuilder('s')
            ->where('s.name LIKE :search')
            ->setParameter('search', '%'.$search.'%')
            ->getQuery()->getResult();
    }
}
