<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class BuildConfigsPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container): void
    {
        $configs = $this->processTagData($container->findTaggedServiceIds('pay.action'), 'pay.action.', 'pay.prepend_actions');
        $configs = array_replace_recursive(
            $configs,
            $this->processTagData($container->findTaggedServiceIds('pay.api'), 'pay.api.', 'pay.prepend_apis')
        );
        $configs = array_replace_recursive(
            $configs,
            $this->processTagData($container->findTaggedServiceIds('pay.extension'), 'pay.extension.', 'pay.prepend_extensions')
        );

        $builder = $container->getDefinition('pay.builder');
        if ($container->hasDefinition('twig')) {
            $config = ['twig.env' => new Reference('twig')];

            $builder->addMethodCall('addCoreGatewayFactoryConfig', [$config]);
        }

        if (empty($configs[0]) === false) {
            $builder->addMethodCall('addCoreGatewayFactoryConfig', [$configs[0]]);
        }

        foreach ($configs[1] as $factoryName => $factoryConfig) {
            $builder->addMethodCall('addGatewayFactoryConfig', [$factoryName, $factoryConfig]);
        }

        foreach ($configs[2] as $gatewayName => $gatewayConfig) {
            $builder->addMethodCall('addGateway', [$gatewayName, $gatewayConfig]);
        }
    }

    protected function processTagData(array $tagData, string $namePrefix, string $prependKey): array
    {
        $coreGatewayFactoryConfig = [];
        $gatewaysFactoriesConfigs = [];
        $gatewaysConfigs = [];

        foreach ($tagData as $serviceId => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                /** @noinspection SlowArrayOperationsInLoopInspection */
                $attributes = array_replace(['alias' => null, 'factory' => null, 'gateway' => null,  'all' => false, 'prepend' => false], $attributes);

                $name = $attributes['alias'] ?: $serviceId;
                $name = $namePrefix . $name;

                if ($attributes['all']) {
                    $coreGatewayFactoryConfig[$name] = "@$serviceId";

                    if ($attributes['prepend']) {
                        if (isset($coreGatewayFactoryConfig[$prependKey]) === false) {
                            $coreGatewayFactoryConfig[$prependKey] = [];
                        }

                        /** @noinspection UnsupportedStringOffsetOperationsInspection */
                        $coreGatewayFactoryConfig[$prependKey][] = $name;
                    }
                } elseif ($attributes['factory']) {
                    if (isset($gatewaysFactoriesConfigs[$attributes['factory']]) === false) {
                        $gatewaysFactoriesConfigs[$attributes['factory']] = [];
                    }

                    $gatewaysFactoriesConfigs[$attributes['factory']][$name] = "@$serviceId";

                    if ($attributes['prepend']) {
                        if (isset($gatewaysFactoriesConfigs[$attributes['factory']][$prependKey]) === false) {
                            $gatewaysFactoriesConfigs[$attributes['factory']][$prependKey] = [];
                        }

                        $gatewaysFactoriesConfigs[$attributes['factory']][$prependKey][] = $name;
                    }
                } elseif ($attributes['gateway']) {
                    if (isset($gatewaysConfigs[$attributes['gateway']]) === false) {
                        $gatewaysConfigs[$attributes['gateway']] = [];
                    }

                    $gatewaysConfigs[$attributes['gateway']][$name] = "@$serviceId";

                    if ($attributes['prepend']) {
                        if (isset($gatewaysConfigs[$attributes['gateway']][$prependKey]) === false) {
                            $gatewaysConfigs[$attributes['gateway']][$prependKey] = [];
                        }

                        $gatewaysConfigs[$attributes['gateway']][$prependKey][] = $name;
                    }
                }
            }
        }

        return [$coreGatewayFactoryConfig, $gatewaysFactoriesConfigs, $gatewaysConfigs];
    }
}
