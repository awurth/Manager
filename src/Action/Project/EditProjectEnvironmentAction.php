<?php

namespace App\Action\Project;

use App\Action\FlashTrait;
use App\Action\RoutingTrait;
use App\Action\TwigTrait;
use App\Form\Type\Action\EditProjectEnvironmentType;
use App\Form\Model\EditProjectEnvironment;
use App\Repository\ProjectEnvironmentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/environment/{id}/edit", requirements={"id": "\d+"}, name="app_project_environment_edit")
 */
class EditProjectEnvironmentAction extends AbstractProjectAction
{
    use FlashTrait;
    use RoutingTrait;
    use TwigTrait;

    private $entityManager;
    private $formFactory;
    private $projectEnvironmentRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        ProjectEnvironmentRepository $projectEnvironmentRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->projectEnvironmentRepository = $projectEnvironmentRepository;
    }

    public function __invoke(Request $request, string $projectGroupSlug, string $projectSlug, int $id): Response
    {
        $this->preInvoke($projectGroupSlug, $projectSlug);

        $this->denyAccessUnlessGranted('MEMBER', $this->project);

        $environment = $this->projectEnvironmentRepository->find($id);

        if (!$environment) {
            throw new NotFoundHttpException('Project environment not found');
        }

        if ($environment->getProject() !== $this->project) {
            throw $this->createAccessDeniedException('The environment does not belong to the current project');
        }

        $model = new EditProjectEnvironment($environment);
        $form = $this->formFactory->create(EditProjectEnvironmentType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $environment->updateFromEditionForm($model);

            $this->entityManager->persist($environment);
            $this->entityManager->flush();

            $this->flash('success', 'flash.success.project.environment.edit');

            return $this->redirectToRoute('app_project_environment_list', [
                'projectGroupSlug' => $this->projectGroup->getSlug(),
                'projectSlug' => $this->project->getSlug()
            ]);
        }

        $this->breadcrumbs
            ->addRouteItem('breadcrumb.project.environment.list', 'app_project_environment_list', [
                'projectGroupSlug' => $this->projectGroup->getSlug(),
                'projectSlug' => $this->project->getSlug()
            ])
            ->addItem($environment->getName(), '', [], false);

        return $this->renderPage('edit-project-environment', 'app/project/edit_environment.html.twig', [
            'environment' => $environment,
            'form' => $form->createView()
        ]);
    }
}
