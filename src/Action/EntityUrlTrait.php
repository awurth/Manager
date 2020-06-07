<?php

namespace App\Action;

use App\Routing\EntityUrlGeneratorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

trait EntityUrlTrait
{
    /**
     * @var EntityUrlGeneratorInterface
     */
    protected $entityUrlGenerator;

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