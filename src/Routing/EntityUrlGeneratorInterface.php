<?php

namespace App\Routing;

interface EntityUrlGeneratorInterface
{
    /**
     * @param mixed  $entity
     * @param string $action
     * @param array  $parameters
     *
     * @return string
     */
    public function generate($entity, string $action, array $parameters = []): string;

    /**
     * @param mixed $entity
     *
     * @return bool
     */
    public function supports($entity): bool;
}
