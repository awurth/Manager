<?php

namespace App\Action\Admin;

use App\Action\FlashTrait;
use App\Action\RoutingTrait;
use App\Action\SecurityTrait;
use App\Action\TwigTrait;
use App\Entity\User;
use App\Form\Type\Action\Admin\CreateUserType;
use App\Form\Model\Admin\CreateUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/users/new", name="app_admin_user_create")
 */
class CreateUserAction extends AbstractAdminAction
{
    use FlashTrait;
    use RoutingTrait;
    use SecurityTrait;
    use TwigTrait;

    private $entityManager;
    private $formFactory;
    private $userPasswordEncoder;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function __invoke(Request $request): Response
    {
        $this->denyAccessUnlessLoggedIn();
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $model = new CreateUser();
        $form = $this->formFactory->create(CreateUserType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = (new User($model->email, $model->firstname, $model->lastname))
                ->setGender($model->gender)
                ->addRole($model->role);

            $user->setPassword($this->userPasswordEncoder->encodePassword($user, $model->plainPassword));

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
