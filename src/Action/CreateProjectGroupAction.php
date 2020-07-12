<?php

namespace App\Action;

use App\Action\Traits\FlashTrait;
use App\Action\Traits\RoutingTrait;
use App\Action\Traits\SecurityTrait;
use App\Action\Traits\TwigTrait;
use App\Entity\ProjectGroup;
use App\Form\Type\Action\CreateProjectGroupType;
use App\Form\Model\CreateProjectGroup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/groups/new", name="app_project_group_create")
 */
class CreateProjectGroupAction
{
    use FlashTrait;
    use RoutingTrait;
    use SecurityTrait;
    use TwigTrait;

    private EntityManagerInterface $entityManager;
    private FormFactoryInterface$formFactory;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
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
            $group = ProjectGroup::createFromCreationForm($model, $this->getUser());

            $this->entityManager->persist($group);
            $this->entityManager->flush();

            $this->flash('success', 'flash.success.project_group.create');

            return $this->redirectToRoute('app_project_group_view', [
                'slug' => $group->getSlug()
            ]);
        }

        return $this->renderPage('create-project-group', 'app/project_group/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
