<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\DependencyInjection\Compiler;

use Onlishop\Bundle\PayBundle\Exception\LogicException;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class BuildStoragesPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $registry = $container->getDefinition('pay.static_registry');

        $servicesIds = [];
        foreach ($container->findTaggedServiceIds('pay.storage') as $serviceId => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                if (isset($attributes['model_class']) === false) {
                    throw new LogicException('The pay.storage tag require model_class attribute.');
                }

                $servicesIds[$attributes['model_class']] = $serviceId;
            }
        }

        $registry->replaceArgument(1, $servicesIds);
    }
}
