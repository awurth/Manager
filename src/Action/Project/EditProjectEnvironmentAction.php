<?php

namespace App\Action\Project;

use App\Action\AbstractAction;
use App\Action\SecurityTrait;
use App\Form\Type\EditProjectEnvironmentType;
use App\Form\Model\EditProjectEnvironment;
use App\Repository\ProjectEnvironmentRepository;
use App\Repository\ProjectRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/project/{slug}/environment/{id}/edit", requirements={"id": "\d+"}, name="app_project_environment_edit")
 */
class EditProjectEnvironmentAction extends AbstractAction
{
    use SecurityTrait;

    private $entityManager;
    private $flashBag;
    private $formFactory;
    private $projectEnvironmentRepository;
    private $projectRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        FormFactoryInterface $formFactory,
        ProjectEnvironmentRepository $projectEnvironmentRepository,
        ProjectRepository $projectRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->formFactory = $formFactory;
        $this->projectEnvironmentRepository = $projectEnvironmentRepository;
        $this->projectRepository = $projectRepository;
    }

    public function __invoke(Request $request, string $slug, int $id): Response
    {
        $this->denyAccessUnlessLoggedIn();

        $project = $this->projectRepository->findOneBy(['slug' => $slug]);

        if (!$project) {
            throw $this->createNotFoundException('Project not found');
        }

        $environment = $this->projectEnvironmentRepository->find($id);

        if (!$environment) {
            throw $this->createNotFoundException('Project environment not found');
        }

        $this->denyAccessUnlessGranted('MEMBER', $project);

        $model = new EditProjectEnvironment($environment);
        $form = $this->formFactory->create(EditProjectEnvironmentType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $environment
                ->setServer($model->server)
                ->setName($model->name)
                ->setPath($model->path)
                ->setUrl($model->url)
                ->setDescription($model->description);

            $this->entityManager->persist($environment);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'flash.success.project.environment.edit');

            return $this->redirectToRoute('app_project_environment_list', ['slug' => $project->getSlug()]);
        }

        return $this->renderPage('edit-project-environment', 'app/project/edit_environment.html.twig', [
            'environment' => $environment,
            'form' => $form->createView()
        ]);
    }
}
