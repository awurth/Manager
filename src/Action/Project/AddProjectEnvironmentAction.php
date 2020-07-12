<?php

namespace App\Action\Project;

use App\Action\Traits\FlashTrait;
use App\Action\Traits\RoutingTrait;
use App\Action\Traits\TwigTrait;
use App\Entity\ProjectEnvironment;
use App\Form\Type\Action\AddProjectEnvironmentType;
use App\Form\Model\AddProjectEnvironment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/environments/new", name="app_project_environment_add")
 */
class AddProjectEnvironmentAction extends AbstractProjectAction
{
    use FlashTrait;
    use RoutingTrait;
    use TwigTrait;

    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }

    public function __invoke(Request $request, string $projectGroupSlug, string $projectSlug): Response
    {
        $this->preInvoke($projectGroupSlug, $projectSlug);

        $this->denyAccessUnlessGranted('MEMBER', $this->project);

        $this->breadcrumbs
            ->addRouteItem('breadcrumb.project.environment.list', 'app_project_environment_list', [
                'projectGroupSlug' => $this->projectGroup->getSlug(),
                'projectSlug' => $this->project->getSlug()
            ])
            ->addItem('breadcrumb.project.environment.create');

        $model = new AddProjectEnvironment();
        $form = $this->formFactory->create(AddProjectEnvironmentType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $environment = ProjectEnvironment::createFromCreationForm($model, $this->project);

            $this->entityManager->persist($environment);
            $this->entityManager->flush();

            $this->flash('success', 'flash.success.project.environment.create');

            return $this->redirectToRoute('app_project_environment_list', [
                'projectGroupSlug' => $this->projectGroup->getSlug(),
                'projectSlug' => $this->project->getSlug()
            ]);
        }

        return $this->renderPage('add-project-environment', 'app/project/add_environment.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
