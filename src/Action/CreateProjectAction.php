<?php

namespace App\Action;

use App\Entity\Project;
use App\Entity\ProjectMember;
use App\Form\Type\Action\CreateProjectType;
use App\Form\Model\CreateProject;
use App\Upload\StorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects/new", name="app_project_create")
 */
class CreateProjectAction
{
    use RoutingTrait;
    use SecurityTrait;
    use TwigTrait;

    private $entityManager;
    private $formFactory;
    private $flashBag;
    private $projectLogoStorage;

    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        FormFactoryInterface $formFactory,
        StorageInterface $projectLogoStorage
    )
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->flashBag = $flashBag;
        $this->projectLogoStorage = $projectLogoStorage;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_PROJECT_CREATE');

        $model = new CreateProject();
        $form = $this->formFactory->create(CreateProjectType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project = (new Project($model->slug, $model->name))
                ->setDescription($model->description)
                ->setProjectGroup($model->projectGroup)
                ->setType($model->type);

            $project->addMember(
                (new ProjectMember())
                    ->setUser($this->getUser())
                    ->setAccessLevel(ProjectMember::ACCESS_LEVEL_OWNER)
            );

            if ($model->logoFile) {
                $this->projectLogoStorage->upload($model->logoFile, $project);
            }

            $this->entityManager->persist($project);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'flash.success.project.create');

            return $this->redirectToRoute('app_project_view', [
                'projectGroupSlug' => $project->getProjectGroup()->getSlug(),
                'projectSlug' => $project->getSlug()
            ]);
        }

        return $this->renderPage('create-project', 'app/project/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
