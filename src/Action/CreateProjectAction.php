<?php

namespace App\Action;

use App\Entity\Project;
use App\Entity\ProjectMember;
use App\Form\Type\Action\CreateProjectType;
use App\Form\Model\CreateProject;
use Awurth\UploadBundle\Storage\StorageInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects/new", name="app_project_create")
 */
class CreateProjectAction
{
    use FlashTrait;
    use RoutingTrait;
    use SecurityTrait;
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
                $upload = $this->uploader->upload($model->logoFile, $project, 'project_logo');
                $project->setLogoFilename($upload->getFilename());
            }

            $this->entityManager->persist($project);
            $this->entityManager->flush();

            $this->flash('success', 'flash.success.project.create');

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
