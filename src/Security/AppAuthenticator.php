<?php

namespace App\Security;

use App\Form\Type\Action\LoginType;
use App\Repository\UserRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authenticator\AbstractLoginFormAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\PasswordUpgradeBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Credentials\PasswordCredentials;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\PassportInterface;
use Symfony\Component\Security\Http\Util\TargetPathTrait;

final class AppAuthenticator extends AbstractLoginFormAuthenticator
{
    use TargetPathTrait;

    private FormFactoryInterface $formFactory;
    private UrlGeneratorInterface $urlGenerator;
    private UserRepository $userRepository;

    public function __construct(FormFactoryInterface $formFactory, UrlGeneratorInterface $urlGenerator, UserRepository $userRepository)
    {
        $this->formFactory = $formFactory;
        $this->urlGenerator = $urlGenerator;
        $this->userRepository = $userRepository;
    }

    protected function getLoginUrl(Request $request): string
    {
        return $this->urlGenerator->generate('app_login');
    }

    public function authenticate(Request $request): PassportInterface
    {
        $form = $this->formFactory->create(LoginType::class);
        $form->handleRequest($request);

        $email = $form->get('email')->getData();
        $password = $form->get('password')->getData();

        $userBadge = new UserBadge($email, function (string $userIdentifier) {
            return $this->userRepository->findOneBy(['email' => $userIdentifier]);
        });
        $credentials = new PasswordCredentials($password);

        return new Passport($userBadge, $credentials, [
            new PasswordUpgradeBadge($password)
        ]);
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        if ($targetPath = $this->getTargetPath($request->getSession(), $firewallName)) {
            return new RedirectResponse($targetPath);
        }

        return new RedirectResponse($this->urlGenerator->generate('app_home'));
    }
}
