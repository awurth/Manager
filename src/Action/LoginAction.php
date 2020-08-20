<?php

namespace App\Action;

use App\Action\Traits\RoutingTrait;
use App\Action\Traits\SecurityTrait;
use App\Action\Traits\TwigTrait;
use App\Form\Type\Action\LoginType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/login", name="app_login")
 */
final class LoginAction
{
    use RoutingTrait;
    use SecurityTrait;
    use TwigTrait;

    private AuthenticationUtils $authenticationUtils;
    private FormFactoryInterface $formFactory;

    public function __construct(AuthenticationUtils $authenticationUtils, FormFactoryInterface $formFactory)
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->formFactory = $formFactory;
    }

    public function __invoke(): Response
    {
        if ($this->security->isLoggedIn()) {
            return $this->redirectToRoute('app_home');
        }

        $form = $this->formFactory->create(LoginType::class, [
            'username' => $this->authenticationUtils->getLastUsername()
        ]);

        return $this->renderPage('login', 'app/login.html.twig', [
            'form' => $form->createView(),
            'last_username' => $this->authenticationUtils->getLastUsername(),
            'error' => $this->authenticationUtils->getLastAuthenticationError()
        ]);
    }
}
