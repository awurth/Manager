<?php

namespace App\Action\Traits;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;

trait RoutingTrait
{
    /**
     * @var RouterInterface
     */
    protected $router;

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

    /**
     * @required
     */
    public function setRouter(RouterInterface $router): void
    {
        $this->router = $router;
    }
}
