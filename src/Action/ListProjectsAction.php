<?php

namespace App\Action;

use App\Action\Traits\FilterTrait;
use App\Action\Traits\PaginationTrait;
use App\Action\Traits\SecurityTrait;
use App\Action\Traits\TwigTrait;
use App\Form\Filter\ProjectType;
use App\Repository\ProjectRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects", name="app_project_list")
 */
final class ListProjectsAction
{
    use FilterTrait;
    use PaginationTrait;
    use SecurityTrait;
    use TwigTrait;

    private FormFactoryInterface $formFactory;
    private ProjectRepository $projectRepository;

    public function __construct(FormFactoryInterface $formFactory, ProjectRepository $projectRepository)
    {
        $this->formFactory = $formFactory;
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessLoggedIn();

        $filtersForm = $this->formFactory->create(ProjectType::class);
        $queryBuilder = $this->getQueryBuilder();

        $this->filter($queryBuilder, $filtersForm, $request);

        $pager = $this->paginate($queryBuilder, $request);

        return $this->renderPage('list-projects', 'app/project/list.html.twig', [
            'projects' => $pager->getCurrentPageResults(),
            'pager' => $pager,
            'filtersForm' => $filtersForm->createView()
        ]);
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return $this->projectRepository->createQueryBuilder('p')
            ->join('p.members', 'm')
            ->where('m.user = :user')
            ->setParameter('user', $this->getUser()->getId(), 'uuid_binary')
            ->orderBy('p.createdAt', 'DESC');
    }
}
