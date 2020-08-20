<?php

namespace App\Action\ProjectGroup;

use App\Action\Traits\FlashTrait;
use App\Action\Traits\TwigTrait;
use App\Form\Model\ChangeProjectGroupSlug;
use App\Form\Type\Action\ChangeProjectGroupSlugType;
use App\Form\Type\Action\EditProjectGroupType;
use App\Form\Model\EditProjectGroup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/edit", name="app_project_group_edit")
 */
final class EditProjectGroupAction extends AbstractProjectGroupAction
{
    use FlashTrait;
    use TwigTrait;

    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;

    private FormInterface $editForm;
    private FormInterface $slugChangeForm;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }

    public function __invoke(Request $request, string $slug): Response
    {
        $this->preInvoke($slug);

        $this->denyAccessUnlessGranted('EDIT', $this->projectGroup);

        if ($response = $this->handleEditForm($request)) {
            return $response;
        }

        if ($response = $this->handleSlugChangeForm($request)) {
            return $response;
        }

        return $this->renderPage('edit-project-group', 'app/project_group/edit.html.twig', [
            'group' => $this->projectGroup,
            'editForm' => $this->editForm->createView(),
            'slugChangeForm' => $this->slugChangeForm->createView()
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        $this->breadcrumbs->addItem('breadcrumb.project_group.edit');
    }

    private function handleEditForm(Request $request): ?Response
    {
        $model = new EditProjectGroup($this->projectGroup);
        $this->editForm = $this->formFactory->create(EditProjectGroupType::class, $model);
        $this->editForm->handleRequest($request);

        if (!$this->editForm->isSubmitted() || !$this->editForm->isValid()) {
            return null;
        }

        $this->projectGroup->updateFromEditionForm($model);

        $this->entityManager->persist($this->projectGroup);
        $this->entityManager->flush();

        $this->flash('success', 'flash.success.project_group.edit');

        return $this->redirectToEntity($this->projectGroup, 'edit');
    }

    private function handleSlugChangeForm(Request $request): ?Response
    {
        $model = new ChangeProjectGroupSlug($this->projectGroup);
        $this->slugChangeForm = $this->formFactory->create(ChangeProjectGroupSlugType::class, $model);
        $this->slugChangeForm->handleRequest($request);

        if (!$this->slugChangeForm->isSubmitted() || !$this->slugChangeForm->isValid()) {
            return null;
        }

        $this->projectGroup->updateFromSlugChangeForm($model);

        $this->entityManager->persist($this->projectGroup);
        $this->entityManager->flush();

        $this->flash('success', 'flash.success.project_group.change_slug');

        return $this->redirectToEntity($this->projectGroup, 'edit');
    }
}
