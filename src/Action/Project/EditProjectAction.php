<?php

namespace App\Action\Project;

use App\Action\RoutingTrait;
use App\Action\TwigTrait;
use App\Form\Type\Action\EditProjectType;
use App\Form\Model\EditProject;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/group/{projectGroupSlug}/project/{projectSlug}/edit", name="app_project_edit")
 */
class EditProjectAction extends AbstractProjectAction
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

    public function __invoke(Request $request, string $projectGroupSlug, string $projectSlug): Response
    {
        $this->preInvoke($projectGroupSlug, $projectSlug);

        $this->denyAccessUnlessGranted('MEMBER', $this->project);

        $model = new EditProject($this->project);
        $form = $this->formFactory->create(EditProjectType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->project
                ->setDescription($model->description)
                ->setName($model->name)
                ->setType($model->type);

            if ($model->imageFilename) {
                $this->project->setImageFilename($model->imageFilename);
            }

            $this->entityManager->persist($this->project);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'flash.success.project.edit');

            return $this->redirectToRoute('app_project_view', [
                'projectGroupSlug' => $this->projectGroup->getSlug(),
                'projectSlug' => $this->project->getSlug()
            ]);
        }

        return $this->renderPage('edit-project', 'app/project/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
