<?php declare(strict_types=1);

namespace HeyPay\Bundle\PayBundle\DependencyInjection;

use HeyPay\Bundle\PayBundle\Core\Bridge\Defuse\Security\DefuseCypher;
use HeyPay\Bundle\PayBundle\Core\Exception\InvalidArgumentException;
use HeyPay\Bundle\PayBundle\Core\Exception\LogicException;
use HeyPay\Bundle\PayBundle\Core\Registry\DynamicRegistry;
use HeyPay\Bundle\PayBundle\Core\Storage\CryptoStorageDecorator;
use HeyPay\Bundle\PayBundle\DependencyInjection\Factory\Storage\FilesystemStorageFactory;
use HeyPay\Bundle\PayBundle\DependencyInjection\Factory\Storage\StorageFactoryInterface;
use HeyPay\Bundle\PayBundle\ReplyToSymfonyResponseConverter;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class PayExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @var StorageFactoryInterface[]
     */
    protected array $storagesFactories = [];

    public function __construct()
    {
        $this->addStorageFactory(new FilesystemStorageFactory());
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $mainConfig = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($mainConfig, $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $loader->load('pay.xml');
        if ($container->getParameter('kernel.debug')) {
            $loader->load('debug.xml');
        }

        $this->loadStorages($config['storages'], $container);
        $this->loadSecurity($config['security'], $container);

        $this->loadCoreGateway($config['gateways']['core'] ?? [], $container);
        unset($config['gateways']['core']);

        $this->loadGateways($config['gateways'], $container);

        if (isset($config['dynamic_gateways'])) {
            $this->loadDynamicGateways($config['dynamic_gateways'], $container);
        }
    }

    public function prepend(ContainerBuilder $container): void
    {
        // TODO: Implement prepend() method.
    }

    /**
     * {@inheritDoc}
     */
    public function getConfiguration(array $config, ContainerBuilder $container): MainConfiguration
    {
        return new MainConfiguration($this->storagesFactories);
    }

    public function addStorageFactory(StorageFactoryInterface $factory): void
    {
        $factoryName = $factory->getName();
        if (empty($factoryName)) {
            throw new InvalidArgumentException(\sprintf('The storage factory %s has empty name', $factory::class));
        }
        if (\array_key_exists($factoryName, $this->storagesFactories)) {
            throw new InvalidArgumentException(\sprintf('The storage factory with such name %s already registered', $factoryName));
        }

        $this->storagesFactories[$factoryName] = $factory;
    }

    protected function loadDynamicGateways(array $dynamicGatewaysConfig, ContainerBuilder $container): void
    {
        foreach ($dynamicGatewaysConfig['config_storage'] as $configClass => $configStorageConfig) {
            $storageFactoryName = $this->findSelectedStorageFactoryNameInStorageConfig($configStorageConfig);

            $configStorage = $this->storagesFactories[$storageFactoryName]->create(
                $container,
                $configClass,
                $configStorageConfig[$storageFactoryName]
            );

            $container->setDefinition('pay.dynamic_gateways.config_storage', new ChildDefinition($configStorage));
        }

        if (isset($dynamicGatewaysConfig['encryption']['defuse_secret_key'])) {
            $container->register('pay.dynamic_gateways.cypher', DefuseCypher::class)
                ->addArgument($dynamicGatewaysConfig['encryption']['defuse_secret_key'])
            ;
            $container->register('pay.dynamic_gateways.encrypted_config_storage', CryptoStorageDecorator::class)
                ->setPublic(false)
                ->setDecoratedService('pay.dynamic_gateways.config_storage')
                ->addArgument(new Reference('pay.dynamic_gateways.encrypted_config_storage.inner'))
                ->addArgument(new Reference('pay.dynamic_gateways.cypher'))
            ;
        }

        // deprecated
        $registry = new Definition(DynamicRegistry::class, [
            new Reference('pay.dynamic_gateways.config_storage'),
            new Reference('pay.static_registry'),
        ]);
        $registry->setPublic(true);

        $container->setDefinition('pay.dynamic_registry', $registry);

        $payBuilder = $container->getDefinition('pay.builder');
        $payBuilder->addMethodCall('setGatewayConfigStorage', [new Reference('pay.dynamic_gateways.config_storage')]);
    }

    protected function loadGateways(array $config, ContainerBuilder $container): void
    {
        $builder = $container->getDefinition('pay.builder');

        foreach ($config as $gatewayName => $gatewayConfig) {
            $builder->addMethodCall('addGateway', [$gatewayName, $gatewayConfig]);
        }
    }

    protected function loadCoreGateway(array $config, ContainerBuilder $container): void
    {
        $builder = $container->getDefinition('pay.builder');

        $defaultConfig = [
            'pay.paths' => [
                'PaySymfonyBridge' => \dirname((new \ReflectionClass(ReplyToSymfonyResponseConverter::class))->getFileName()) . '/Resources/views',
            ],

            'pay.action.get_http_request' => new Reference('pay.action.get_http_request'),
        ];

        $config = array_replace_recursive($defaultConfig, $config);

        $builder->addMethodCall('addCoreGatewayFactoryConfig', [$config]);
    }

    protected function loadSecurity(array $securityConfig, ContainerBuilder $container): void
    {
        foreach ($securityConfig['token_storage'] as $tokenClass => $tokenStorageConfig) {
            $storageFactoryName = $this->findSelectedStorageFactoryNameInStorageConfig($tokenStorageConfig);

            $storageId = $this->storagesFactories[$storageFactoryName]->create(
                $container,
                $tokenClass,
                $tokenStorageConfig[$storageFactoryName]
            );

            $container->setDefinition('pay.security.token_storage', new ChildDefinition($storageId));
        }
    }

    protected function loadStorages(array $config, ContainerBuilder $container): void
    {
        foreach ($config as $modelClass => $storageConfig) {
            $storageFactoryName = $this->findSelectedStorageFactoryNameInStorageConfig($storageConfig);
            $storageId = $this->storagesFactories[$storageFactoryName]->create(
                $container,
                $modelClass,
                $storageConfig[$storageFactoryName]
            );

            $container->getDefinition($storageId)->addTag('pay.storage', ['model_class' => $modelClass]);

            if (str_contains($storageId, '.storage.')) {
                $storageExtensionId = str_replace('.storage.', '.extension.storage.', $storageId);
            } else {
                throw new LogicException(\sprintf('In order to add storage to extension the storage "%s" has to contains ".storage." inside.', $storageId));
            }

            $storageExtension = new ChildDefinition('pay.extension.storage.prototype');
            $storageExtension->replaceArgument(0, new Reference($storageId));
            $storageExtension->setPublic(true);
            $container->setDefinition($storageExtensionId, $storageExtension);

            if ($storageConfig['extension']['all']) {
                $storageExtension->addTag('pay.extension', ['all' => true]);
            } else {
                foreach ($storageConfig['extension']['gateways'] as $gatewayName) {
                    $storageExtension->addTag('pay.extension', ['gateway' => $gatewayName]);
                }

                foreach ($storageConfig['extension']['factories'] as $factory) {
                    $storageExtension->addTag('pay.extension', ['factory' => $factory]);
                }
            }
        }
    }

    protected function findSelectedStorageFactoryNameInStorageConfig(array $storageConfig): string
    {
        foreach ($storageConfig as $name => $value) {
            if (isset($this->storagesFactories[$name])) {
                return $name;
            }
        }

        throw new \RuntimeException('StorageFactoryName not found');
    }
}
