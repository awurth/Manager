<?php

namespace App\Action\Project;

use App\Action\Traits\FlashTrait;
use App\Action\Traits\TwigTrait;
use App\Entity\ValueObject\Id;
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
 * @Route("/environment/{id}/edit", name="app_project_environment_edit")
 */
final class EditProjectEnvironmentAction extends AbstractProjectAction
{
    use FlashTrait;
    use TwigTrait;

    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;
    private ProjectEnvironmentRepository $projectEnvironmentRepository;

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

    public function __invoke(Request $request, string $projectGroupSlug, string $projectSlug, string $id): Response
    {
        $this->preInvoke($projectGroupSlug, $projectSlug);

        $this->denyAccessUnlessGranted('MEMBER', $this->project);

        $environment = $this->projectEnvironmentRepository->find(Id::fromString($id));

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

            return $this->redirectToEntity($this->project, 'environment_list');
        }

        $this->breadcrumbs
            ->addItem('breadcrumb.project.environment.list', $this->entityUrlGenerator->generate($this->project, 'environment_list'))
            ->addItem($environment->getName(), '', [], false);

        return $this->renderPage('edit-project-environment', 'app/project/edit_environment.html.twig', [
            'environment' => $environment,
            'form' => $form->createView()
        ]);
    }
}
