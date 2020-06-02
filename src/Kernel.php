<?php

namespace App;

use App\Routing\ChainEntityUrlGenerator;
use App\Routing\EntityUrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

class Kernel extends BaseKernel implements CompilerPassInterface
{
    use MicroKernelTrait;

    protected function configureContainer(ContainerConfigurator $container): void
    {
        $container->import('../config/{packages}/*.yaml');
        $container->import('../config/{packages}/'.$this->environment.'/*.yaml');
        $container->import('../config/{services}.yaml');
        $container->import('../config/{services}_'.$this->environment.'.yaml');
    }

    protected function configureRoutes(RoutingConfigurator $routes): void
    {
        $routes->import('../config/{routes}/'.$this->environment.'/*.yaml');
        $routes->import('../config/{routes}/*.yaml');
        $routes->import('../config/{routes}.yaml');
    }

    protected function build(ContainerBuilder $container): void
    {
        $container->registerForAutoconfiguration(EntityUrlGeneratorInterface::class)
            ->addTag('app.entity_url_generator');
    }

    public function process(ContainerBuilder $container): void
    {
        $this->processEntityUrlGenerators($container);
    }

    private function processEntityUrlGenerators(ContainerBuilder $container): void
    {
        $generators = [];
        foreach ($container->findTaggedServiceIds('app.entity_url_generator') as $id => $tags) {
            if ($id !== ChainEntityUrlGenerator::class) {
                $generators[] = new Reference($id);
            }
        }

        $generators = new IteratorArgument($generators);

        $definition = $container->getDefinition(ChainEntityUrlGenerator::class);
        $definition->setArgument(0, $generators);
        $container->setAlias(EntityUrlGeneratorInterface::class, ChainEntityUrlGenerator::class);
    }
}
