<?php

namespace App\Action\Project;

use App\Action\RoutingTrait;
use App\Action\TwigTrait;
use App\Entity\Link;
use App\Form\Model\EditProjectLink;
use App\Form\Type\Action\EditProjectLinkType;
use App\Repository\LinkRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/link/{id}/edit", requirements={"id": "\d+"}, name="app_project_link_edit")
 */
class EditProjectLinkAction extends AbstractProjectAction
{
    use RoutingTrait;
    use TwigTrait;

    private $entityManager;
    private $flashBag;
    private $formFactory;
    private $linkRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        FormFactoryInterface $formFactory,
        LinkRepository $linkRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->formFactory = $formFactory;
        $this->linkRepository = $linkRepository;
    }

    public function __invoke(Request $request, string $projectGroupSlug, string $projectSlug, int $id): Response
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
            $link
                ->setName($model->name)
                ->setUri($model->uri)
                ->setLinkType($model->linkType);

            $this->entityManager->persist($link);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'flash.success.project.link.edit');

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
            ->addItem($link->getUri(), '', [], false);

        return $this->renderPage('edit-project-link', 'app/project/edit_link.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
