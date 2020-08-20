<?php

namespace App\Action\ProjectGroup;

use App\Action\Traits\FlashTrait;
use App\Action\Traits\PaginationTrait;
use App\Action\Traits\TwigTrait;
use App\Entity\ProjectGroupMember;
use App\Form\Type\Action\AddProjectGroupMemberType;
use App\Form\Model\AddProjectGroupMember;
use App\Repository\ProjectGroupMemberRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/members", name="app_project_group_members")
 */
final class ProjectGroupMembersAction extends AbstractProjectGroupAction
{
    use FlashTrait;
    use PaginationTrait;
    use TwigTrait;

    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;
    private ProjectGroupMemberRepository $projectGroupMemberRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        ProjectGroupMemberRepository $projectGroupMemberRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->projectGroupMemberRepository = $projectGroupMemberRepository;
    }

    public function __invoke(Request $request, string $slug): Response
    {
        $this->preInvoke($slug);

        $this->denyAccessUnlessGranted('MEMBER', $this->projectGroup);

        $model = new AddProjectGroupMember($this->projectGroup);
        $form = $this->formFactory->create(AddProjectGroupMemberType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $member = ProjectGroupMember::createFromGroupMemberAdditionForm($model);

            $this->entityManager->persist($member);
            $this->entityManager->flush();

            $this->flash('success', 'flash.success.project_group.member.add');

            return $this->redirectToEntity($this->projectGroup, 'members');
        }

        $pager = $this->paginate($this->getQueryBuilder(), $request);

        return $this->renderPage('project-group-members', 'app/project_group/members.html.twig', [
            'form' => $form->createView(),
            'group' => $this->projectGroup,
            'members' => $pager->getCurrentPageResults(),
            'pager' => $pager
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        $this->breadcrumbs->addItem('breadcrumb.project_group.members');
    }

    private function getQueryBuilder(): QueryBuilder
    {
        return $this->projectGroupMemberRepository->createQueryBuilder('m')
            ->join('m.user', 'u')->addSelect('u')
            ->where('m.projectGroup = :group')
            ->setParameter('group', $this->projectGroup->getId(), 'uuid_binary')
            ->orderBy('m.accessLevel', 'DESC')
            ->addOrderBy('m.createdAt', 'DESC');
    }
}
