<?php

namespace App\Action\Project;

use App\Action\RoutingTrait;
use App\Action\TwigTrait;
use App\Entity\ProjectEnvironment;
use App\Form\Type\Action\CreateProjectEnvironmentType;
use App\Form\Model\CreateProjectEnvironment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/environments/new", name="app_project_environment_add")
 */
class AddProjectEnvironmentAction extends AbstractProjectAction
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

        $this->breadcrumbs
            ->addRouteItem('breadcrumb.project.environment.list', 'app_project_environment_list', [
                'projectGroupSlug' => $this->projectGroup->getSlug(),
                'projectSlug' => $this->project->getSlug()
            ])
            ->addItem('breadcrumb.project.environment.create');

        $model = new CreateProjectEnvironment();
        $form = $this->formFactory->create(CreateProjectEnvironmentType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->project->addEnvironment(
                (new ProjectEnvironment($model->name, $model->path))
                    ->setServer($model->server)
                    ->setUrl($model->url)
                    ->setDescription($model->description)
            );

            $this->entityManager->persist($this->project);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'flash.success.project.environment.create');

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
