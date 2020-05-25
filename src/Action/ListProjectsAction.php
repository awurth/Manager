<?php

namespace App\Action;

use App\Repository\ProjectRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects", name="app_project_list")
 */
class ListProjectsAction
{
    use PaginationTrait;
    use SecurityTrait;
    use TwigTrait;

    private $projectRepository;

    public function __construct(ProjectRepository $projectRepository)
    {
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessLoggedIn();

        $pager = $this->paginate($this->getQueryBuilder(), $request);

        return $this->renderPage('list-projects', 'app/project/list.html.twig', [
            'projects' => $pager->getCurrentPageResults(),
            'pager' => $pager
        ]);
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return $this->projectRepository->createQueryBuilder('p')
            ->join('p.members', 'm')
            ->where('m.user = :user')
            ->setParameter('user', $this->getUser())
            ->orderBy('p.createdAt', 'DESC');
    }
}
