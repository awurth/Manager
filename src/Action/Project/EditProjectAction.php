<?php

namespace App\Action\Project;

use App\Action\FlashTrait;
use App\Action\RoutingTrait;
use App\Action\TwigTrait;
use App\Form\Type\Action\EditProjectType;
use App\Form\Model\EditProject;
use Awurth\UploadBundle\Storage\StorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/edit", name="app_project_edit")
 */
class EditProjectAction extends AbstractProjectAction
{
    use FlashTrait;
    use RoutingTrait;
    use TwigTrait;

    private $entityManager;
    private $formFactory;
    private $uploader;

    public function __construct(
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        StorageInterface $uploader
    )
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->uploader = $uploader;
    }

    public function __invoke(Request $request, string $projectGroupSlug, string $projectSlug): Response
    {
        $this->preInvoke($projectGroupSlug, $projectSlug);

        $this->denyAccessUnlessGranted('EDIT', $this->project);

        $model = new EditProject($this->project);
        $form = $this->formFactory->create(EditProjectType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->project
                ->setDescription($model->description)
                ->setName($model->name)
                ->setType($model->type);

            if ($model->logoFile) {
                $upload = $this->uploader->upload($model->logoFile, $this->project, 'project_logo');
                $this->project->setLogoFilename($upload->getFilename());
            }

            $this->entityManager->persist($this->project);
            $this->entityManager->flush();

            $this->flash('success', 'flash.success.project.edit');

            return $this->redirectToRoute('app_project_view', [
                'projectGroupSlug' => $this->projectGroup->getSlug(),
                'projectSlug' => $this->project->getSlug()
            ]);
        }

        return $this->renderPage('edit-project', 'app/project/edit.html.twig', [
            'project' => $this->project,
            'form' => $form->createView()
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        $this->breadcrumbs->addItem('breadcrumb.project.edit');
    }
}
