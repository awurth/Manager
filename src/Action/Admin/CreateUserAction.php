<?php

namespace App\Action\Admin;

use App\Action\AbstractAction;
use App\Entity\User;
use App\Form\Type\CreateUserType;
use App\Form\Model\CreateUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/users/new", name="app_admin_user_create")
 */
class CreateUserAction extends AbstractAction
{
    private $entityManager;
    private $flashBag;
    private $formFactory;
    private $userPasswordEncoder;

    public function __construct(EntityManagerInterface $entityManager, FlashBagInterface $flashBag, FormFactoryInterface $formFactory, UserPasswordEncoderInterface $userPasswordEncoder)
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
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
            $user = (new User($model->email))
                ->setGender($model->gender)
                ->setFirstname($model->firstname)
                ->setLastname($model->lastname)
                ->addRole($model->role);

            $user->setPassword($this->userPasswordEncoder->encodePassword($user, $model->plainPassword));

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'flash.success.user.create');

            return $this->redirectToRoute('app_admin_user_list');
        }

        return $this->renderPage('admin-create-user', 'app/admin/create_user.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
