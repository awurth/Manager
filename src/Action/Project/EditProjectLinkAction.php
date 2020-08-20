<?php

namespace App\Action\Project;

use App\Action\Traits\FlashTrait;
use App\Action\Traits\TwigTrait;
use App\Form\Model\EditProjectLink;
use App\Form\Type\Action\EditProjectLinkType;
use App\Repository\LinkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/link/{id}/edit", name="app_project_link_edit")
 */
final class EditProjectLinkAction extends AbstractProjectAction
{
    use FlashTrait;
    use TwigTrait;

    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;
    private LinkRepository $linkRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        LinkRepository $linkRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->linkRepository = $linkRepository;
    }

    public function __invoke(Request $request, string $projectGroupSlug, string $projectSlug, string $id): Response
    {
        $this->preInvoke($projectGroupSlug, $projectSlug);

        $this->denyAccessUnlessGranted('MEMBER', $this->project);

        $link = $this->linkRepository->find($id);

        if (!$link) {
            throw new NotFoundHttpException('Link not found');
        }

        if ($link->getProject() !== $this->project) {
            throw $this->createAccessDeniedException('The link does not belong to the current project');
        }

        $model = new EditProjectLink($link);
        $form = $this->formFactory->create(EditProjectLinkType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $link->updateFromProjectLinkEditionForm($model);

            $this->entityManager->persist($link);
            $this->entityManager->flush();

            $this->flash('success', 'flash.success.project.link.edit');

            return $this->redirectToEntity($this->project, 'link_list');
        }

        $this->breadcrumbs
            ->addItem('breadcrumb.project.link.list', $this->entityUrlGenerator->generate($this->project, 'link_list'))
            ->addItem($link->getName(), '', [], false);

        return $this->renderPage('edit-project-link', 'app/project/edit_link.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
