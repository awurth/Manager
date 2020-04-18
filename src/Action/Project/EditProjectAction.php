<?php

namespace App\Action\Project;

use App\Action\AbstractAction;
use App\Form\EditProjectType;
use App\Form\Model\EditProject;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/project/{slug}/edit", name="app_project_edit")
 */
class EditProjectAction extends AbstractAction
{
    private $entityManager;
    private $flashBag;
    private $formFactory;
    private $projectRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        FormFactoryInterface $formFactory,
        ProjectRepository $projectRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->formFactory = $formFactory;
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(Request $request, string $slug): Response
    {
        $this->denyAccessUnlessLoggedIn();

        $project = $this->projectRepository->findOneBy(['slug' => $slug]);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        $this->denyAccessUnlessGranted('MEMBER', $project);

        $model = new EditProject($project);
        $form = $this->formFactory->create(EditProjectType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $project
                ->setCustomer($model->customer)
                ->setDescription($model->description)
                ->setName($model->name)
                ->setType($model->type);

            if ($model->imageFilename) {
                $project->setImageFilename($model->imageFilename);
            }

            $this->entityManager->persist($project);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'flash.success.project.edit');

            return $this->redirectToRoute('app_project', ['slug' => $project->getSlug()]);
        }

        return $this->renderPage('edit-project', 'app/project/edit_project.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
