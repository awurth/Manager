<?php

namespace App\Routing;

interface EntityUrlGeneratorInterface
{
    public function generate($entity, string $action, array $parameters = []): string;

    public function supports($entity): bool;
}
