<?php

namespace App\Action\Traits;

use App\Routing\EntityUrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

trait EntityUrlTrait
{
    protected EntityUrlGeneratorInterface $entityUrlGenerator;

    /**
     * @param mixed  $entity
     * @param string $routeName
     * @param array  $parameters
     * @param int    $status
     *
     * @return RedirectResponse
     */
    protected function redirectToEntity($entity, string $routeName, array $parameters = [], int $status = 302): RedirectResponse
    {
        return new RedirectResponse($this->entityUrlGenerator->generate($entity, $routeName, $parameters), $status);
    }

    /**
     * @required
     */
    public function setEntityUrlGenerator(EntityUrlGeneratorInterface $entityUrlGenerator): void
    {
        $this->entityUrlGenerator = $entityUrlGenerator;
    }
}
