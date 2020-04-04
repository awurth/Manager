<?php

namespace App\Action\Admin;

use App\Action\AbstractAction;
use App\Form\Admin\LoginType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

/**
 * @Route("/login", name="admin_login")
 */
class LoginAction extends AbstractAction
{
    private $authenticationUtils;
    private $formFactory;

    public function __construct(AuthenticationUtils $authenticationUtils, FormFactoryInterface $formFactory)
    {
        $this->authenticationUtils = $authenticationUtils;
        $this->formFactory = $formFactory;
    }

    public function __invoke(): Response
    {
        if ($this->isGranted('IS_AUTHENTICATED_REMEMBERED')) {
            return $this->redirectToRoute('sonata_admin_dashboard');
        }

        $form = $this->formFactory->create(LoginType::class, [
            'username' => $this->authenticationUtils->getLastUsername()
        ]);

        return $this->render('admin/security/login.html.twig', [
            'form' => $form->createView(),
            'last_username' => $this->authenticationUtils->getLastUsername(),
            'error' => $this->authenticationUtils->getLastAuthenticationError()
        ]);
    }
}
