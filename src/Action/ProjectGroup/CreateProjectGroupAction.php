<?php

namespace App\Action\ProjectGroup;

use App\Action\AbstractAction;
use App\Action\SecurityTrait;
use App\Entity\ProjectGroup;
use App\Entity\ProjectGroupMember;
use App\Form\Type\CreateProjectGroupType;
use App\Form\Model\CreateProjectGroup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/groups/new", name="app_project_group_create")
 */
class CreateProjectGroupAction extends AbstractAction
{
    use SecurityTrait;

    private $formFactory;
    private $flashBag;
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager, FlashBagInterface $flashBag, FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->entityManager = $entityManager;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_PROJECT_GROUP_CREATE');

        $model = new CreateProjectGroup();
        $form = $this->formFactory->create(CreateProjectGroupType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $group = (new ProjectGroup($model->slug, $model->name))
                ->setCustomer($model->customer)
                ->setDescription($model->description);

            $group->addMember(
                (new ProjectGroupMember())
                    ->setUser($this->getUser())
                    ->setAccessLevel(ProjectGroupMember::ACCESS_LEVEL_OWNER)
            );

            $this->entityManager->persist($group);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'flash.success.project_group.create');

            return $this->redirectToRoute('app_project_group_view', [
                'slug' => $group->getSlug()
            ]);
        }

        return $this->renderPage('create-project-group', 'app/project_group/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
