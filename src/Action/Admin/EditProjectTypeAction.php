<?php

namespace App\Action\Admin;

use App\Action\RoutingTrait;
use App\Action\SecurityTrait;
use App\Action\TwigTrait;
use App\Form\Model\EditProjectType;
use App\Form\Type\Action\EditProjectTypeType;
use App\Repository\ProjectTypeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects/type/{id}/edit", requirements={"id": "\d+"}, name="app_admin_project_type_edit")
 */
class EditProjectTypeAction extends AbstractAdminAction
{
    use RoutingTrait;
    use SecurityTrait;
    use TwigTrait;

    private $entityManager;
    private $flashBag;
    private $formFactory;
    private $projectTypeRepository;

    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        FormFactoryInterface $formFactory,
        ProjectTypeRepository $projectTypeRepository
    )
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->formFactory = $formFactory;
        $this->projectTypeRepository = $projectTypeRepository;
    }

    public function __invoke(Request $request, int $id): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $projectType = $this->projectTypeRepository->find($id);

        if (!$projectType) {
            throw new NotFoundHttpException('Project type not found');
        }

        $model = new EditProjectType($projectType);
        $form = $this->formFactory->create(EditProjectTypeType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $projectType->setName($model->name);

            $this->entityManager->persist($projectType);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'flash.success.project.type.edit');

            return $this->redirectToRoute('app_admin_project_type_list');
        }

        $this->breadcrumbs->addItem($projectType->getName(), '', [], false);

        return $this->renderPage('admin-edit-project-type', 'app/admin/edit_project_type.html.twig', [
            'form' => $form->createView(),
            'type' => $projectType
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        parent::configureBreadcrumbs();

        $this->breadcrumbs->addRouteItem('breadcrumb.admin.project_type.list', 'app_admin_project_type_list');
    }
}
