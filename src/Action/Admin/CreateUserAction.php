<?php

namespace App\Action\Admin;

use App\Action\Traits\FlashTrait;
use App\Action\Traits\RoutingTrait;
use App\Action\Traits\SecurityTrait;
use App\Action\Traits\TwigTrait;
use App\Entity\User;
use App\Form\Type\Action\Admin\CreateUserType;
use App\Form\Model\Admin\CreateUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/users/new', name: 'app_admin_user_create')]
final class CreateUserAction extends AbstractAdminAction
{
    use FlashTrait;
    use RoutingTrait;
    use SecurityTrait;
    use TwigTrait;

    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, UserPasswordHasherInterface $userPasswordHasher)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $model = new CreateUser();
        $form = $this->formFactory->create(CreateUserType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = User::createFromAdminCreationForm($model, $this->userPasswordHasher);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->flash('success', 'flash.success.user.create');

            return $this->redirectToRoute('app_admin_user_list');
        }

        return $this->renderPage('admin-create-user', 'app/admin/create_user.html.twig', [
            'form' => $form->createView()
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        parent::configureBreadcrumbs();

        $this->breadcrumbs
            ->addRouteItem('breadcrumb.admin.user.list', 'app_admin_user_list')
            ->addItem('breadcrumb.admin.user.create');
    }
}
