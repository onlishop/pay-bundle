<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\DependencyInjection\Compiler;

use Onlishop\Bundle\PayBundle\Exception\LogicException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BuildGatewayFactoriesPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $registry = $container->getDefinition('pay.static_registry');

        $servicesIds = [];
        foreach ($container->findTaggedServiceIds('pay.gateway_factory') as $serviceId => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                if (isset($attributes['factory']) === false) {
                    throw new LogicException('The pay.gateway_factory tag require factory attribute.');
                }

                $servicesIds[$attributes['factory']] = $serviceId;
            }
        }

        $registry->replaceArgument(2, $servicesIds);
    }
}
