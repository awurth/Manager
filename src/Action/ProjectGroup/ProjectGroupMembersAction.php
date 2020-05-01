<?php

namespace App\Action\ProjectGroup;

use App\Action\RoutingTrait;
use App\Action\TwigTrait;
use App\Entity\ProjectGroupMember;
use App\Form\Type\AddProjectGroupMemberType;
use App\Form\Model\AddProjectGroupMember;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/group/{slug}/members", name="app_project_group_members")
 */
class ProjectGroupMembersAction extends AbstractProjectGroupAction
{
    use RoutingTrait;
    use TwigTrait;

    private $entityManager;
    private $flashBag;
    private $formFactory;

    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        FormFactoryInterface $formFactory
    )
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->formFactory = $formFactory;
    }

    public function __invoke(Request $request, string $slug): Response
    {
        $this->preInvoke($slug);

        $this->denyAccessUnlessGranted('MEMBER', $this->projectGroup);

        $model = new AddProjectGroupMember($this->projectGroup);
        $form = $this->formFactory->create(AddProjectGroupMemberType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->projectGroup->addMember(
                (new ProjectGroupMember())
                    ->setUser($model->user)
                    ->setAccessLevel($model->accessLevel)
            );

            $this->entityManager->persist($this->projectGroup);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'flash.success.project_group.member.add');

            return $this->redirectToRoute('app_project_group_members', ['slug' => $this->projectGroup->getSlug()]);
        }

        return $this->renderPage('project-group-members', 'app/project_group/members.html.twig', [
            'form' => $form->createView(),
            'group' => $this->projectGroup
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        $this->breadcrumbs->addItem('breadcrumb.project_group.members');
    }
}
