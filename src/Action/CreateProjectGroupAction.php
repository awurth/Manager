<?php

namespace App\Action;

use App\Entity\ProjectGroup;
use App\Entity\ProjectGroupMember;
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

    private $formFactory;
    private $entityManager;

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
            $group = (new ProjectGroup($model->slug, $model->name))
                ->setClient($model->client)
                ->setDescription($model->description);

            $group->addMember(
                (new ProjectGroupMember())
                    ->setUser($this->getUser())
                    ->setAccessLevel(ProjectGroupMember::ACCESS_LEVEL_OWNER)
            );

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
