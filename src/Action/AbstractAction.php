<?php

namespace App\Action;

use App\Entity\User;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Security;
use Throwable;
use Twig\Environment;

abstract class AbstractAction
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var Security
     */
    protected $security;

    /**
     * @var Environment
     */
    protected $twig;

    protected function createNotFoundException(string $message = 'Not Found', ?Throwable $previous = null): NotFoundHttpException
    {
        return new NotFoundHttpException($message, $previous);
    }

    protected function createAccessDeniedException(string $message = 'Access Denied.', ?Throwable $previous = null): AccessDeniedException
    {
        return new AccessDeniedException($message, $previous);
    }

    protected function denyAccessUnlessLoggedIn(): void
    {
        if (!$this->isLoggedIn()) {
            throw new HttpException(401);
        }
    }

    protected function denyAccessUnlessGranted(string $attribute, $subject = null, string $message = 'Access Denied.'): void
    {
        if (!$this->isGranted($attribute, $subject)) {
            $exception = $this->createAccessDeniedException($message);
            $exception->setAttributes($attribute);
            $exception->setSubject($subject);

            throw $exception;
        }
    }

    protected function file($file, ?string $fileName = null, string $disposition = ResponseHeaderBag::DISPOSITION_ATTACHMENT): BinaryFileResponse
    {
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition($disposition, $fileName ?? $response->getFile()->getFilename());

        return $response;
    }

    protected function getUser(): User
    {
        /** @noinspection PhpIncompatibleReturnTypeInspection */
        return $this->security->getUser();
    }

    protected function isGranted(string $attribute, $subject = null): bool
    {
        return $this->security->isGranted($attribute, $subject);
    }

    protected function isLoggedIn(): bool
    {
        return $this->isGranted('IS_AUTHENTICATED_REMEMBERED');
    }

    protected function json($data, int $status = 200, array $headers = []): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }

    protected function redirect(string $url, int $status = 302): RedirectResponse
    {
        return new RedirectResponse($url, $status);
    }

    protected function redirectToRoute(string $route, array $parameters = [], int $status = 302): RedirectResponse
    {
        return $this->redirect($this->router->generate($route, $parameters), $status);
    }

    protected function render(string $template, array $parameters = [], Response $response = null): Response
    {
        if (null === $response) {
            $response = new Response();
        }

        $response->setContent($this->twig->render($template, $parameters));

        return $response;
    }

    protected function renderPage(string $page, string $template, array $parameters = [], Response $response = null): Response
    {
        $parameters['page_name'] = $page;

        return $this->render($template, $parameters, $response);
    }

    /**
     * @required
     */
    public function setRouter(RouterInterface $router): void
    {
        $this->router = $router;
    }

    /**
     * @required
     */
    public function setSecurity(Security $security): void
    {
        $this->security = $security;
    }

    /**
     * @required
     */
    public function setTwig(Environment $twig): void
    {
        $this->twig = $twig;
    }
}
