<?php

namespace App\Action;

use App\Action\Traits\EntityUrlTrait;
use App\Action\Traits\FlashTrait;
use App\Action\Traits\SecurityTrait;
use App\Action\Traits\TwigTrait;
use App\Entity\Project;
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
    use EntityUrlTrait;
    use FlashTrait;
    use SecurityTrait;
    use TwigTrait;

    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;
    private StorageInterface $uploader;

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
            $project = Project::createFromCreationForm($model, $this->getUser(), $this->uploader);

            $this->entityManager->persist($project);
            $this->entityManager->flush();

            $this->flash('success', 'flash.success.project.create');

            return $this->redirectToEntity($project, 'view');
        }

        return $this->renderPage('create-project', 'app/project/create.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
