<?php

namespace App\Action\Project;

use App\Action\RoutingTrait;
use App\Action\TwigTrait;
use App\Form\Type\Action\EditProjectEnvironmentType;
use App\Form\Model\EditProjectEnvironment;
use App\Repository\ProjectEnvironmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/environment/{id}/edit", requirements={"id": "\d+"}, name="app_project_environment_edit")
 */
class EditProjectEnvironmentAction extends AbstractProjectAction
{
    use RoutingTrait;
    use TwigTrait;

    private $entityManager;
    private $flashBag;
    private $formFactory;
    private $projectEnvironmentRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        FormFactoryInterface $formFactory,
        ProjectEnvironmentRepository $projectEnvironmentRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->formFactory = $formFactory;
        $this->projectEnvironmentRepository = $projectEnvironmentRepository;
    }

    public function __invoke(Request $request, string $projectGroupSlug, string $projectSlug, int $id): Response
    {
        $this->preInvoke($projectGroupSlug, $projectSlug);

        $environment = $this->projectEnvironmentRepository->find($id);

        if (!$environment) {
            throw new NotFoundHttpException('Project environment not found');
        }

        $this->denyAccessUnlessGranted('MEMBER', $this->project);

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

            return $this->redirectToRoute('app_project_environment_list', [
                'projectGroupSlug' => $this->projectGroup->getSlug(),
                'projectSlug' => $this->project->getSlug()
            ]);
        }

        return $this->renderPage('edit-project-environment', 'app/project/edit_environment.html.twig', [
            'environment' => $environment,
            'form' => $form->createView()
        ]);
    }
}
