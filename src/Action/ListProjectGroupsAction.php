<?php

namespace App\Action;

use App\Action\Traits\PaginationTrait;
use App\Action\Traits\SecurityTrait;
use App\Action\Traits\TwigTrait;
use App\Repository\ProjectGroupRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/groups", name="app_project_group_list")
 */
class ListProjectGroupsAction
{
    use PaginationTrait;
    use SecurityTrait;
    use TwigTrait;

    private ProjectGroupRepository $projectGroupRepository;

    public function __construct(ProjectGroupRepository $projectGroupRepository)
    {
        $this->projectGroupRepository = $projectGroupRepository;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessLoggedIn();

        $pager = $this->paginate($this->getQueryBuilder(), $request);

        return $this->renderPage('list-project-groups', 'app/project_group/list.html.twig', [
            'groups' => $pager->getCurrentPageResults(),
            'pager' => $pager
        ]);
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return $this->projectGroupRepository->createQueryBuilder('g')
            ->join('g.members', 'm')
            ->where('m.user = :user')
            ->setParameter('user', $this->getUser()->getId(), 'uuid_binary')
            ->orderBy('g.createdAt', 'DESC');
    }
}
