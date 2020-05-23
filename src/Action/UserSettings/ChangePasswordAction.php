<?php

namespace App\Action\UserSettings;

use App\Action\RoutingTrait;
use App\Action\TwigTrait;
use App\Form\Model\ChangePassword;
use App\Form\Type\Action\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * @Route("/password", name="app_change_password")
 */
class ChangePasswordAction extends AbstractUserSettingsAction
{
    use RoutingTrait;
    use TwigTrait;

    private $entityManager;
    private $flashBag;
    private $formFactory;
    private $userPasswordEncoder;

    public function __construct(
        EntityManagerInterface $entityManager,
        FlashBagInterface $flashBag,
        FormFactoryInterface $formFactory,
        UserPasswordEncoderInterface $userPasswordEncoder
    )
    {
        $this->entityManager = $entityManager;
        $this->flashBag = $flashBag;
        $this->formFactory = $formFactory;
        $this->userPasswordEncoder = $userPasswordEncoder;
    }

    public function __invoke(Request $request): Response
    {
        $this->preInvoke();

        $model = new ChangePassword();
        $form = $this->formFactory->create(ChangePasswordType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->getUser();

            $user->setPassword($this->userPasswordEncoder->encodePassword($user, $model->newPassword));

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'flash.success.password.change');

            return $this->redirectToRoute('app_change_password');
        }

        return $this->renderPage('change-password', 'app/user_settings/change-password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        $this->breadcrumbs->addItem('breadcrumb.user_settings.password');
    }
}
