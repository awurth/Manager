<?php

namespace App\Action;

use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\RouterInterface;
use Throwable;
use Twig\Environment;

abstract class AbstractAction
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var Environment
     */
    protected $twig;

    protected function createNotFoundException(string $message = 'Not Found', ?Throwable $previous = null): NotFoundHttpException
    {
        return new NotFoundHttpException($message, $previous);
    }

    protected function file($file, ?string $fileName = null, string $disposition = ResponseHeaderBag::DISPOSITION_ATTACHMENT): BinaryFileResponse
    {
        $response = new BinaryFileResponse($file);
        $response->setContentDisposition($disposition, $fileName ?? $response->getFile()->getFilename());

        return $response;
    }

    protected function getReferer(Request $request): ?string
    {
        if ($referer = $request->headers->get('Referer')) {
            if (false !== $pos = strpos($referer, '?')) {
                $referer = substr($referer, 0, $pos);
            }

            return $referer ?: null;
        }

        return null;
    }

    protected function json($data, int $status = 200, array $headers = []): JsonResponse
    {
        return new JsonResponse($data, $status, $headers);
    }

    protected function redirect(string $url, int $status = 302): RedirectResponse
    {
        return new RedirectResponse($url, $status);
    }

    protected function redirectToReferer(Request $request, string $fallbackRoute, array $parameters = [], int $status = 302): RedirectResponse
    {
        $referer = $this->getReferer($request);
        return $referer
            ? $this->redirect($referer, $status)
            : $this->redirect($this->router->generate($fallbackRoute, $parameters), $status);
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
    public function setTwig(Environment $twig): void
    {
        $this->twig = $twig;
    }
}
