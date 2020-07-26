<?php


namespace App\DependencyInjection\Compiler;

use App\Routing\ChainEntityUrlGenerator;
use App\Routing\EntityUrlGeneratorInterface;
use Symfony\Component\DependencyInjection\Argument\IteratorArgument;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class EntityUrlGeneratorCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
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
