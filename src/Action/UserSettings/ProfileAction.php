<?php

namespace App\Action\UserSettings;

use App\Action\Traits\FlashTrait;
use App\Action\Traits\RoutingTrait;
use App\Action\Traits\TwigTrait;
use App\Form\Model\EditProfile;
use App\Form\Type\Action\EditProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="app_profile")
 */
final class ProfileAction extends AbstractUserSettingsAction
{
    use FlashTrait;
    use RoutingTrait;
    use TwigTrait;

    private EntityManagerInterface $entityManager;
    private FormFactoryInterface $formFactory;

    public function __construct(EntityManagerInterface $entityManager, FormFactoryInterface $formFactory)
    {
        $this->entityManager = $entityManager;
        $this->formFactory = $formFactory;
    }

    public function __invoke(Request $request): Response
    {
        $this->preInvoke();

        $user = $this->security->getUser();

        $model = new EditProfile($user);
        $form = $this->formFactory->create(EditProfileType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->updateFromProfileEditionForm($model);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->flash('success', 'flash.success.profile.edit');

            return $this->redirectToRoute('app_profile');
        }

        return $this->renderPage('profile', 'app/user_settings/profile.html.twig', [
            'form' => $form->createView()
        ]);
    }

    protected function configureBreadcrumbs(): void
    {
        $this->breadcrumbs->addItem('breadcrumb.user_settings.profile');
    }
}
