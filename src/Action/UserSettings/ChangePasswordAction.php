<?php

namespace App\Action\UserSettings;

use App\Action\Traits\FlashTrait;
use App\Action\Traits\RoutingTrait;
use App\Action\Traits\TwigTrait;
use App\Form\Model\ChangePassword;
use App\Form\Type\Action\ChangePasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/password", name="app_change_password")
 */
final class ChangePasswordAction extends AbstractUserSettingsAction
{
    use FlashTrait;
    use RoutingTrait;
    use TwigTrait;

    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;
    private UserPasswordHasherInterface $userPasswordHasher;

    public function __construct(
        EntityManagerInterface $entityManager,
        FormFactoryInterface $formFactory,
        UserPasswordHasherInterface $userPasswordHasher
    )
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
        $this->userPasswordHasher = $userPasswordHasher;
    }

    public function __invoke(Request $request): Response
    {
        $this->preInvoke();

        $model = new ChangePassword();
        $form = $this->formFactory->create(ChangePasswordType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $this->security->getUser();

            $user->updateFromPasswordChangeForm($model, $this->userPasswordHasher);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->flash('success', 'flash.success.password.change');

            return $this->redirectToRoute('app_change_password');
        }

        return $this->renderPage('change-password', 'app/user_settings/change_password.html.twig', [
            'form' => $form->createView()
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        $this->breadcrumbs->addItem('breadcrumb.user_settings.password');
    }
}
