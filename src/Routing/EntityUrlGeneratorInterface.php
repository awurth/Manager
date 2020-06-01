<?php

namespace App\Routing;

interface EntityUrlGeneratorInterface
{
    public function generate($entity, string $action, array $parameters = []);

    public function supports($entity): bool;
}
