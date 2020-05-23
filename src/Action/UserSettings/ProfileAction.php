<?php

namespace App\Action\UserSettings;

use App\Action\RoutingTrait;
use App\Action\TwigTrait;
use App\Form\Model\EditProfile;
use App\Form\Type\Action\EditProfileType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="app_profile")
 */
class ProfileAction extends AbstractUserSettingsAction
{
    use RoutingTrait;
    use TwigTrait;

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
        $this->preInvoke();

        $user = $this->getUser();

        $model = new EditProfile($user);
        $form = $this->formFactory->create(EditProfileType::class, $model);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user
                ->setEmail($model->email)
                ->setGender($model->gender)
                ->setFirstname($model->firstname)
                ->setLastname($model->lastname);

            $this->entityManager->persist($user);
            $this->entityManager->flush();

            $this->flashBag->add('success', 'flash.success.profile.edit');

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
