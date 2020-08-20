<?php

namespace App\Action\Project;

use App\Action\Traits\FlashTrait;
use App\Action\Traits\TwigTrait;
use App\Entity\Link;
use App\Form\Model\AddProjectLink;
use App\Form\Type\Action\AddProjectLinkType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/links/new", name="app_project_link_add")
 */
final class AddProjectLinkAction extends AbstractProjectAction
{
    use FlashTrait;
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

        $model = new AddProjectLink();
        $form = $this->formFactory->create(AddProjectLinkType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $link = Link::createFromProjectLinkCreationForm($model, $this->project);

            $this->entityManager->persist($link);
            $this->entityManager->flush();

            $this->flash('success', 'flash.success.project.link.create');

            return $this->redirectToEntity($this->project, 'link_list');
        }

        $this->breadcrumbs
            ->addItem('breadcrumb.project.link.list', $this->entityUrlGenerator->generate($this->project, 'link_list'))
            ->addItem('breadcrumb.project.link.create');

        return $this->renderPage('add-project-link', 'app/project/add_link.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
