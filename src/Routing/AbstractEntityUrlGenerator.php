<?php

namespace App\Routing;

use Symfony\Component\Routing\RouterInterface;

abstract class AbstractEntityUrlGenerator implements EntityUrlGeneratorInterface
{
    protected $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }
}
