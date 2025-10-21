<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle;

use DI\ContainerBuilder;
use Onlishop\Bundle\PayBundle\Bridge\Spl\ArrayObject;
use Onlishop\Bundle\PayBundle\DI\ContainerConfiguration;
use Onlishop\Bundle\PayBundle\Exception\LogicException;
use Onlishop\Bundle\PayBundle\Extension\StorageExtension;
use Onlishop\Bundle\PayBundle\Gateways\WeChat\WeCahtGatewayFactory;
use Onlishop\Bundle\PayBundle\Model\GatewayConfigInterface;
use Onlishop\Bundle\PayBundle\Model\Token;
use Onlishop\Bundle\PayBundle\Registry\DynamicRegistry;
use Onlishop\Bundle\PayBundle\Registry\FallbackRegistry;
use Onlishop\Bundle\PayBundle\Registry\RegistryInterface;
use Onlishop\Bundle\PayBundle\Registry\SimpleRegistry;
use Onlishop\Bundle\PayBundle\Registry\StorageRegistryInterface;
use Onlishop\Bundle\PayBundle\Security\TokenFactory;
use Onlishop\Bundle\PayBundle\Security\TokenFactoryInterface;
use Onlishop\Bundle\PayBundle\Security\TokenInterface;
use Onlishop\Bundle\PayBundle\Storage\FilesystemStorage;
use Onlishop\Bundle\PayBundle\Storage\StorageInterface;

/**
 * @template StorageType of object
 *
 * @implements RegistryInterface<StorageType>
 */
class PayBuilder
{
    /**
     * @var TokenFactoryInterface|callable|null
     */
    protected $tokenFactory;

    /**
     * @var StorageInterface<object>[]
     */
    protected array $storages = [];

    /**
     * @var ?RegistryInterface<object>
     */
    protected ?RegistryInterface $mainRegistry = null;

    /**
     * @var array<string, mixed>
     */
    protected array $gatewayFactoryConfigs = [];

    /**
     * @var ?StorageInterface<TokenInterface>
     */
    protected ?StorageInterface $tokenStorage = null;

    /**
     * @var ContainerConfiguration[]|callable[]
     */
    protected array $gatewayFactories = [];

    /**
     * @var array<string, mixed>
     */
    protected array $gatewayConfigs = [];

    /**
     * @var GatewayInterface[]
     */
    protected array $gateways = [];

    /**
     * @var ContainerConfiguration|callable|null
     */
    protected $coreGatewayFactory;

    /**
     * @var GatewayConfigInterface[]
     */
    protected array $coreGatewayFactoryConfig = [];

    /**
     * @var ?StorageInterface<GatewayConfigInterface>
     */
    protected ?StorageInterface $gatewayConfigStorage = null;

    /**
     * @var string[]
     */
    protected array $genericTokenFactoryPaths = [];

    /**
     * @throws \Exception
     *
     * @return Pay<StorageRegistryInterface<StorageInterface<TokenInterface>>>
     */
    public function getPay(): Pay
    {
        if (!$this->tokenStorage) {
            $this->addDefaultStorages();
        }

        $coreGatewayFactory = $this->buildCoreGatewayFactory(array_replace_recursive([
            'pay.security.token_storage' => $this->tokenStorage,
        ], $this->coreGatewayFactoryConfig));

        $gatewayFactories = array_replace(
            $this->buildGatewayFactories($coreGatewayFactory),
            $this->buildAddedGatewayFactories($coreGatewayFactory)
        );

        $gatewayFactories['core'] = $coreGatewayFactory;

        $registry = $this->buildRegistry($this->gateways, $this->storages, $gatewayFactories);
        if (\count($this->gatewayConfigs)) {
            $gateways = $this->gateways;

            foreach ($this->gatewayConfigs as $name => $gatewayConfig) {
                $containerBuilder = new ContainerBuilder();

                $containerBuilder->addDefinitions($gatewayConfig);
                $containerBuilder->addDefinitions([
                    'pay.security.token_storage' => fn () => $this->tokenStorage,
                ]);

                $factoryName = $gatewayConfig['factory'];

                $gatewayFactory = $registry->getGatewayFactory($factoryName);
                unset($gatewayConfig['factory']);

                $containerBuilder->addDefinitions($gatewayFactory->configureContainer());

                $gateways[$name] = $gatewayFactory->createGateway($containerBuilder->build());
            }
            $registry = $this->buildRegistry($gateways, $this->storages, $gatewayFactories);
        }

        return new Pay($registry, $this->tokenStorage);
    }

    public function addDefaultStorages(): static
    {
        /** @var StorageInterface<TokenInterface> $tokenStorage */
        $tokenStorage = new FilesystemStorage(sys_get_temp_dir(), Token::class, 'hash');

        $this
            ->setTokenStorage($tokenStorage)
            ->addStorage(Pay::class, new FilesystemStorage(sys_get_temp_dir(), Pay::class, 'number'))
            ->addStorage(ArrayObject::class, new FilesystemStorage(sys_get_temp_dir(), ArrayObject::class));

        return $this;
    }

    /**
     * @param class-string $modelClass
     * @param StorageInterface<object> $storage
     */
    public function addStorage(string $modelClass, StorageInterface $storage): static
    {
        $this->storages[$modelClass] = $storage;

        return $this;
    }

    /**
     * @param GatewayInterface|array<string, mixed> $gateway
     */
    public function addGateway(string $name, GatewayInterface|array $gateway): static
    {
        if ($gateway instanceof GatewayInterface) {
            $this->gateways[$name] = $gateway;
        } else {
            $currentConfig = $this->gatewayConfigs[$name] ?? [];
            $currentConfig = array_replace_recursive($currentConfig, $gateway);
            if (empty($currentConfig['factory'])) {
                throw new \InvalidArgumentException('Gateway config must have factory set in it and it must not be empty.');
            }

            $this->gatewayConfigs[$name] = $currentConfig;
        }

        return $this;
    }

    public function addGatewayFactory(string $name, callable|ContainerConfiguration $gatewayFactory): static
    {
        $this->gatewayFactories[$name] = $gatewayFactory;

        return $this;
    }

    /**
     * @param array<string, mixed> $config
     */
    public function addGatewayFactoryConfig(string $name, array $config): static
    {
        $currentConfig = $this->gatewayFactoryConfigs[$name] ?? [];
        $this->gatewayFactoryConfigs[$name] = array_replace_recursive($currentConfig, $config);

        return $this;
    }

    public function setCoreGatewayFactory(callable|ContainerConfiguration|null $coreGatewayFactory = null): static
    {
        $this->coreGatewayFactory = $coreGatewayFactory;

        return $this;
    }

    /**
     * @param array<string, mixed> $config
     *
     * @return $this
     */
    public function setCoreGatewayFactoryConfig(array $config = []): static
    {
        $this->coreGatewayFactoryConfig = $config;

        return $this;
    }

    /**
     * @param array<string, mixed> $config
     */
    public function addCoreGatewayFactoryConfig(array $config): static
    {
        $currentConfig = $this->coreGatewayFactoryConfig ?: [];
        $this->coreGatewayFactoryConfig = array_replace_recursive($currentConfig, $config);

        return $this;
    }

    /**
     * @param StorageInterface<GatewayConfigInterface>|null $gatewayConfigStorage
     */
    public function setGatewayConfigStorage(?StorageInterface $gatewayConfigStorage = null): static
    {
        $this->gatewayConfigStorage = $gatewayConfigStorage;

        return $this;
    }

    /**
     * @param RegistryInterface<object>|null $mainRegistry
     */
    public function setMainRegistry(?RegistryInterface $mainRegistry = null): static
    {
        $this->mainRegistry = $mainRegistry;

        return $this;
    }

    /**
     * @param ?StorageInterface<TokenInterface> $tokenStorage
     */
    public function setTokenStorage(?StorageInterface $tokenStorage = null): static
    {
        $this->tokenStorage = $tokenStorage;

        return $this;
    }

    /**
     * @param string[] $paths
     */
    public function setGenericTokenFactoryPaths(array $paths = []): static
    {
        $this->genericTokenFactoryPaths = $paths;

        return $this;
    }

    public function setTokenFactory(callable|TokenFactoryInterface|null $tokenFactory = null): static
    {
        $this->tokenFactory = $tokenFactory;

        return $this;
    }

    /**
     * @return ContainerConfiguration[]
     */
    protected function buildAddedGatewayFactories(ContainerConfiguration $coreGatewayFactory): array
    {
        $gatewayFactories = [];
        foreach ($this->gatewayFactories as $name => $factory) {
            if (\is_callable($factory)) {
                $config = $this->gatewayFactoryConfigs[$name] ?? [];

                $factory = $factory($config, $coreGatewayFactory);
            }

            $gatewayFactories[$name] = $factory;
        }

        return $gatewayFactories;
    }

    /**
     * @return ContainerConfiguration[]
     */
    protected function buildGatewayFactories(ContainerConfiguration $coreGatewayFactory): array
    {
        $map = [
            'wechat' => WeCahtGatewayFactory::class,
        ];

        $gatewayFactories = [];

        foreach ($map as $name => $factoryClass) {
            if (class_exists($factoryClass)) {
                $gatewayFactories[$name] = new $factoryClass(
                    $this->gatewayFactoryConfigs[$name] ?? [],
                    $coreGatewayFactory
                );
            }
        }

        return $gatewayFactories;
    }

    /**
     * @param StorageInterface<TokenInterface> $tokenStorage
     * @param StorageRegistryInterface<StorageInterface<TokenInterface>> $storageRegistry
     */
    protected function buildTokenFactory(StorageInterface $tokenStorage, StorageRegistryInterface $storageRegistry): TokenFactoryInterface
    {
        $tokenFactory = $this->tokenFactory;

        if (\is_callable($tokenFactory)) {
            $tokenFactory = $tokenFactory($tokenStorage, $storageRegistry);

            if (!$tokenFactory instanceof TokenFactoryInterface) {
                throw new LogicException('Builder returned invalid instance');
            }
        }

        return $tokenFactory ?: new TokenFactory($tokenStorage, $storageRegistry);
    }

    /**
     * @param array<string, GatewayInterface> $gateways
     * @param array<string, StorageInterface<object>> $storages
     *
     * @return RegistryInterface<StorageRegistryInterface<StorageInterface<TokenInterface>>>
     */
    protected function buildRegistry(array $gateways = [], array $storages = [], array $gatewayFactories = []): RegistryInterface
    {
        $registry = new SimpleRegistry($gateways, $storages, $gatewayFactories);

        if ($this->gatewayConfigStorage) {
            $dynamicRegistry = new DynamicRegistry($this->gatewayConfigStorage, $registry);

            $registry = new FallbackRegistry($dynamicRegistry, $registry);
        }

        if ($this->mainRegistry) {
            $registry = new FallbackRegistry($this->mainRegistry, $registry);
        }

        /** @var RegistryInterface<StorageRegistryInterface<StorageInterface<TokenInterface>>> $registry */
        return $registry;
    }

    /**
     * @param array<string, mixed> $config
     */
    private function buildCoreGatewayFactory(array $config): ContainerConfiguration
    {
        $coreGatewayFactory = $this->coreGatewayFactory;

        $storages = $this->storages;
        foreach ($storages as $modelClass => $storage) {
            $extensionName = 'pay.extension.storage_' . strtolower(str_replace('\\', '_', $modelClass));

            $config[$extensionName] = new StorageExtension($storage);
        }

        if (\is_callable($coreGatewayFactory)) {
            $coreGatewayFactory = $coreGatewayFactory($config);

            if (!$coreGatewayFactory instanceof ContainerConfiguration) {
                throw new LogicException('Builder returned invalid instance');
            }
        }

        return $coreGatewayFactory ?: new CoreGatewayFactory($config);
    }
}
