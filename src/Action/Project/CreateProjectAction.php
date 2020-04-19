<?php

namespace App\Action\Project;

use App\Action\AbstractAction;
use App\Entity\Project;
use App\Entity\ProjectMember;
use App\Form\CreateProjectType;
use App\Form\Model\CreateProject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects/new", name="app_project_create")
 */
class CreateProjectAction extends AbstractAction
{
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
        $this->denyAccessUnlessGranted('ROLE_PROJECT_CREATE');

        $model = new CreateProject();
        $form = $this->formFactory->create(CreateProjectType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project = (new Project())
                ->setCustomer($model->customer)
                ->setDescription($model->description)
                ->setImageFilename($model->imageFilename)
                ->setName($model->name)
                ->setSlug($model->slug)
                ->setType($model->type);

            $project->addMember(
                (new ProjectMember())
                    ->setUser($this->getUser())
                    ->setAccessLevel(ProjectMember::ACCESS_LEVEL_OWNER)
            );

            $this->entityManager->persist($project);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'flash.success.project.create');

            return $this->redirectToRoute('app_project', [
                'slug' => $project->getSlug()
            ]);
        }

        return $this->renderPage('create-project', 'app/project/create_project.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
