<?php

namespace App\Action\Traits;

use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

trait TwigTrait
{
    protected Environment $twig;

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
    public function setTwig(Environment $twig): void
    {
        $this->twig = $twig;
    }
}
