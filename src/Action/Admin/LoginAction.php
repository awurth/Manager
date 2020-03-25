<?php

namespace App\Action\Admin;

use App\Action\AbstractAction;
use App\Form\Admin\LoginType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

/**
 * @Route("/login", name="admin_login")
 */
class LoginAction extends AbstractAction
{
    protected $authenticationUtils;
    protected $formFactory;

    public function __construct(
        AuthenticationUtils $authenticationUtils,
        AuthorizationCheckerInterface $authorizationChecker,
        Environment $twig,
        FormFactoryInterface $formFactory,
        RouterInterface $router
    )
    {
        parent::__construct($authorizationChecker, $twig, $router);

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
