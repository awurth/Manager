<?php

namespace App\Action\ProjectGroup;

use App\Action\RoutingTrait;
use App\Action\TwigTrait;
use App\Form\Type\Action\EditProjectGroupType;
use App\Form\Model\EditProjectGroup;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
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

        $model = new EditProjectGroup($this->projectGroup);
        $form = $this->formFactory->create(EditProjectGroupType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->projectGroup
                ->setCustomer($model->customer)
                ->setDescription($model->description)
                ->setName($model->name);

            $this->entityManager->persist($this->projectGroup);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'flash.success.project_group.edit');

            return $this->redirectToRoute('app_project_group_view', ['slug' => $this->projectGroup->getSlug()]);
        }

        return $this->renderPage('edit-project-group', 'app/project_group/edit.html.twig', [
            'form' => $form->createView()
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        $this->breadcrumbs->addItem('breadcrumb.project_group.edit');
    }
}
