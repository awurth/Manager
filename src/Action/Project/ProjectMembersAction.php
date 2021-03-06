<?php

namespace App\Action\Project;

use App\Action\Traits\FlashTrait;
use App\Action\Traits\PaginationTrait;
use App\Action\Traits\TwigTrait;
use App\Entity\ProjectMember;
use App\Form\Model\AddProjectMember;
use App\Form\Type\Action\AddProjectMemberType;
use App\Repository\ProjectMemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/members", name="app_project_members")
 */
final class ProjectMembersAction extends AbstractProjectAction
{
    use FlashTrait;
    use PaginationTrait;
    use TwigTrait;

    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;
    private ProjectMemberRepository $projectMemberRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        ProjectMemberRepository $projectMemberRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->projectMemberRepository = $projectMemberRepository;
    }

    public function __invoke(Request $request, string $projectGroupSlug, string $projectSlug): Response
    {
        $this->preInvoke($projectGroupSlug, $projectSlug);

        $this->denyAccessUnlessGranted('MEMBER', $this->project);

        $model = new AddProjectMember($this->project);
        $form = $this->formFactory->create(AddProjectMemberType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $member = ProjectMember::createFromProjectMemberAdditionForm($model);

            $this->entityManager->persist($member);
            $this->entityManager->flush();

            $this->flash('success', 'flash.success.project.member.add');

            return $this->redirectToEntity($this->project, 'members');
        }

        $pager = $this->paginate($this->getQueryBuilder(), $request);

        return $this->renderPage('project-members', 'app/project/members.html.twig', [
            'form' => $form->createView(),
            'project' => $this->project,
            'members' => $pager->getCurrentPageResults(),
            'pager' => $pager
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        $this->breadcrumbs->addItem('breadcrumb.project.members');
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return $this->projectMemberRepository->createQueryBuilder('m')
            ->join('m.user', 'u')->addSelect('u')
            ->where('m.project = :project')
            ->setParameter('project', $this->project)
            ->orderBy('m.accessLevel', 'DESC')
            ->addOrderBy('m.createdAt', 'DESC');
    }
}
