<?php

namespace App\Action\ProjectGroup;

use App\Action\RoutingTrait;
use App\Action\TwigTrait;
use App\Form\Model\ChangeProjectGroupSlug;
use App\Form\Type\Action\ChangeProjectGroupSlugType;
use App\Form\Type\Action\EditProjectGroupType;
use App\Form\Model\EditProjectGroup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/edit", name="app_project_group_edit")
 */
class EditProjectGroupAction extends AbstractProjectGroupAction
{
    use RoutingTrait;
    use TwigTrait;

    private $entityManager;
    private $flashBag;
    private $formFactory;

    /**
     * @var FormInterface
     */
    private $editForm;

    /**
     * @var FormInterface
     */
    private $slugChangeForm;

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

    public function __invoke(Request $request, string $slug): Response
    {
        $this->preInvoke($slug);

        $this->denyAccessUnlessGranted('MEMBER', $this->projectGroup);

        if ($response = $this->handleEditForm($request)) {
            return $response;
        }

        if ($response = $this->handleSlugChangeForm($request)) {
            return $response;
        }

        return $this->renderPage('edit-project-group', 'app/project_group/edit.html.twig', [
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

        $this->projectGroup
            ->setCustomer($model->customer)
            ->setDescription($model->description)
            ->setName($model->name);

        $this->entityManager->persist($this->projectGroup);
        $this->entityManager->flush();

        $this->flashBag->add('success', 'flash.success.project_group.edit');

        return $this->redirectToRoute('app_project_group_edit', ['slug' => $this->projectGroup->getSlug()]);
    }

    private function handleSlugChangeForm(Request $request): ?Response
    {
        $model = new ChangeProjectGroupSlug();
        $this->slugChangeForm = $this->formFactory->create(ChangeProjectGroupSlugType::class, $model);
        $this->slugChangeForm->handleRequest($request);

        if (!$this->slugChangeForm->isSubmitted() || !$this->slugChangeForm->isValid()) {
            return null;
        }

        $this->projectGroup->setSlug($model->slug);

        $this->entityManager->persist($this->projectGroup);
        $this->entityManager->flush();

        $this->flashBag->add('success', 'flash.success.project_group.change_slug');

        return $this->redirectToRoute('app_project_group_edit', ['slug' => $this->projectGroup->getSlug()]);
    }
}
