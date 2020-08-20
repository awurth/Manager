<?php

namespace App\Routing;

use InvalidArgumentException;

final class ChainEntityUrlGenerator implements EntityUrlGeneratorInterface
{
    private iterable $generators;

    /**
     * @param EntityUrlGeneratorInterface[]|iterable $generators
     */
    public function __construct(iterable $generators)
    {
        $this->generators = $generators;
    }

    public function generate($entity, string $action, array $parameters = []): string
    {
        foreach ($this->generators as $generator) {
            if ($generator->supports($entity)) {
                return $generator->generate($entity, $action, $parameters);
            }
        }

        throw new InvalidArgumentException(sprintf('There is no URL generator for entity of type "%s"', get_class($entity)));
    }

    public function supports($entity): bool
    {
        foreach ($this->generators as $generator) {
            if ($generator->supports($entity)) {
                return true;
            }
        }

        return false;
    }
}
