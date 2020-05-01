<?php

namespace App\Action\ProjectGroup;

use App\Action\RoutingTrait;
use App\Action\SecurityTrait;
use App\Action\TwigTrait;
use App\Entity\ProjectGroupMember;
use App\Form\Type\AddProjectGroupMemberType;
use App\Form\Model\AddProjectGroupMember;
use App\Repository\ProjectGroupRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/group/{slug}/members", name="app_project_group_members")
 */
class ProjectGroupMembersAction
{
    use RoutingTrait;
    use SecurityTrait;
    use TwigTrait;

    private $entityManager;
    private $flashBag;
    private $formFactory;
    private $projectGroupRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        FormFactoryInterface $formFactory,
        ProjectGroupRepository $projectGroupRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->formFactory = $formFactory;
        $this->projectGroupRepository = $projectGroupRepository;
    }

    public function __invoke(Request $request, string $slug): Response
    {
        $this->denyAccessUnlessLoggedIn();

        $group = $this->projectGroupRepository->findOneBy(['slug' => $slug]);

        if (!$group) {
            throw new NotFoundHttpException('Project not found');
        }

        $this->denyAccessUnlessGranted('MEMBER', $group);

        $model = new AddProjectGroupMember($group);
        $form = $this->formFactory->create(AddProjectGroupMemberType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $group->addMember(
                (new ProjectGroupMember())
                    ->setUser($model->user)
                    ->setAccessLevel($model->accessLevel)
            );

            $this->entityManager->persist($group);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'flash.success.project_group.member.add');

            return $this->redirectToRoute('app_project_group_view', ['slug' => $group->getSlug()]);
        }

        return $this->renderPage('project-group-members', 'app/project_group/members.html.twig', [
            'form' => $form->createView(),
            'group' => $group
        ]);
    }
}
