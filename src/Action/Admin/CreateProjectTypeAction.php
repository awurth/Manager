<?php

namespace App\Action\Admin;

use App\Action\AbstractAction;
use App\Action\SecurityTrait;
use App\Entity\ProjectType;
use App\Form\Model\CreateProjectType;
use App\Form\Type\CreateProjectTypeType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/projects/types/new", name="app_admin_project_type_create")
 */
class CreateProjectTypeAction extends AbstractAction
{
    use SecurityTrait;

    private $entityManager;
    private $flashBag;
    private $formFactory;

    public function __construct(EntityManagerInterface $entityManager, FlashBagInterface $flashBag, FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
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

            $this->flashBag->add('success', 'flash.success.project.type.create');

            return $this->redirectToRoute('app_admin_project_type_list');
        }

        return $this->renderPage('admin-create-project-type', 'app/admin/create_project_type.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
