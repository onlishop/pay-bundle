<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\DependencyInjection\Compiler;

use Onlishop\Bundle\PayBundle\Exception\LogicException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class BuildGatewayFactoriesBuilderPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $builder = $container->getDefinition('pay.builder');
        foreach ($container->findTaggedServiceIds('pay.gateway_factory_builder') as $serviceId => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                if (isset($attributes['factory']) === false) {
                    throw new LogicException('The pay.gateway_factory tag require factory attribute.');
                }

                $builder->addMethodCall('addGatewayFactory', [$attributes['factory'], new Reference($serviceId)]);
            }
        }
    }
}
