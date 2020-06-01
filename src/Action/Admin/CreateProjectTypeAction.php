<?php

namespace App\Action\Admin;

use App\Action\FlashTrait;
use App\Action\RoutingTrait;
use App\Action\SecurityTrait;
use App\Action\TwigTrait;
use App\Entity\ProjectType;
use App\Form\Model\Admin\CreateProjectType;
use App\Form\Type\Action\Admin\CreateProjectTypeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects/types/new", name="app_admin_project_type_create")
 */
class CreateProjectTypeAction extends AbstractAdminAction
{
    use FlashTrait;
    use RoutingTrait;
    use SecurityTrait;
    use TwigTrait;

    private $entityManager;
    private $formFactory;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $model = new CreateProjectType();
        $form = $this->formFactory->create(CreateProjectTypeType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $projectType = new ProjectType($model->name);

            $this->entityManager->persist($projectType);
            $this->entityManager->flush();

            $this->flash('success', 'flash.success.project.type.create');

            return $this->redirectToRoute('app_admin_project_type_list');
        }

        return $this->renderPage('admin-create-project-type', 'app/admin/create_project_type.html.twig', [
            'form' => $form->createView()
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        parent::configureBreadcrumbs();

        $this->breadcrumbs
            ->addRouteItem('breadcrumb.admin.project_type.list', 'app_admin_project_type_list')
            ->addItem('breadcrumb.admin.project_type.create');
    }
}
