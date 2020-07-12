<?php

namespace App\Twig;

use App\Routing\EntityUrlGeneratorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class EntityUrlGeneratorExtension extends AbstractExtension
{
    private EntityUrlGeneratorInterface $entityUrlGenerator;

    public function __construct(EntityUrlGeneratorInterface $entityUrlGenerator)
    {
        $this->entityUrlGenerator = $entityUrlGenerator;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('path_to', [$this, 'getPathTo'])
        ];
    }

    public function getPathTo($entity, string $action, array $parameters = []): ?string
    {
        return $this->entityUrlGenerator->generate($entity, $action, $parameters);
    }
}
