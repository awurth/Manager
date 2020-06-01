<?php

namespace App\Action\Project;

use App\Action\RoutingTrait;
use App\Action\TwigTrait;
use App\Entity\Link;
use App\Form\Model\AddProjectLink;
use App\Form\Type\Action\AddProjectLinkType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/links/new", name="app_project_link_add")
 */
class AddProjectLinkAction extends AbstractProjectAction
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

        $model = new AddProjectLink();
        $form = $this->formFactory->create(AddProjectLinkType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->project->addLink(
                (new Link($model->name, $model->uri))
                    ->setLinkType($model->linkType)
            );

            $this->entityManager->persist($this->project);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'flash.success.project.link.create');

            return $this->redirectToRoute('app_project_link_list', [
                'projectGroupSlug' => $this->projectGroup->getSlug(),
                'projectSlug' => $this->project->getSlug()
            ]);
        }

        $this->breadcrumbs
            ->addRouteItem('breadcrumb.project.link.list', 'app_project_link_list', [
                'projectGroupSlug' => $this->projectGroup->getSlug(),
                'projectSlug' => $this->project->getSlug()
            ])
            ->addItem('breadcrumb.project.link.create');

        return $this->renderPage('add-project-link', 'app/project/add_link.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
