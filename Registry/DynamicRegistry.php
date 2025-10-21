<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Registry;

use DI\ContainerBuilder;
use Onlishop\Bundle\PayBundle\DI\ContainerConfiguration;
use Onlishop\Bundle\PayBundle\GatewayFactoryConfigInterface;
use Onlishop\Bundle\PayBundle\GatewayInterface;
use Onlishop\Bundle\PayBundle\Model\GatewayConfigInterface;
use Onlishop\Bundle\PayBundle\Storage\StorageInterface;

/**
 * @template StorageType of object
 *
 * @implements RegistryInterface<StorageType>
 */
class DynamicRegistry implements RegistryInterface
{
    /**
     * @var GatewayInterface[]
     */
    private array $gateways = [];

    /**
     * @var StorageInterface<GatewayFactoryConfigInterface>
     */
    private StorageInterface $gatewayConfigStore;

    /**
     * @var GatewayFactoryRegistryInterface|RegistryInterface<StorageType>
     */
    private GatewayFactoryRegistryInterface|RegistryInterface $gatewayFactoryRegistry;

    /**
     * @param StorageInterface<GatewayConfigInterface> $gatewayConfigStore
     */
    public function __construct(StorageInterface $gatewayConfigStore, GatewayFactoryRegistryInterface $gatewayFactoryRegistry)
    {
        $this->gatewayConfigStore = $gatewayConfigStore;
        $this->gatewayFactoryRegistry = $gatewayFactoryRegistry;
    }

    public function getGatewayFactory(string $name): ContainerConfiguration
    {
        if ($this->gatewayFactoryRegistry instanceof RegistryInterface) {
            return $this->gatewayFactoryRegistry->getGatewayFactory($name);
        }

        throw new \InvalidArgumentException(\sprintf('Gateway factory "%s" does not exist.', $name));
    }

    public function getGatewayFactories(): array
    {
        if ($this->gatewayFactoryRegistry instanceof RegistryInterface) {
            return $this->gatewayFactoryRegistry->getGatewayFactories();
        }

        return [];
    }

    public function getGateway(string $name): GatewayInterface
    {
        if (\array_key_exists($name, $this->gateways)) {
            return $this->gateways[$name];
        }

        if ($gatewayConfigs = $this->gatewayConfigStore->findBy([
            'gatewayName' => $name,
        ])) {
            $gateway = $this->createGateway(array_shift($gatewayConfigs));
            $this->gateways[$name] = $gateway;

            return $gateway;
        }

        if ($this->gatewayFactoryRegistry instanceof RegistryInterface) {
            return $this->gatewayFactoryRegistry->getGateway($name);
        }

        throw new \InvalidArgumentException(\sprintf('Gateway "%s" does not exist.', $name));
    }

    /**
     * @return GatewayInterface[]
     */
    public function getGateways(): array
    {
        if ($this->gatewayFactoryRegistry instanceof RegistryInterface) {
            return $this->gatewayFactoryRegistry->getGateways();
        }

        $gateways = [];
        foreach ($this->gatewayConfigStore->findBy([]) as $gatewayConfig) {
            /** @var GatewayConfigInterface $gatewayConfig */
            $gateways[$gatewayConfig->getGatewayName()] = $this->getGateway($gatewayConfig->getGatewayName());
        }

        return $gateways;
    }

    /**
     * @param class-string|object $class
     *
     * @return StorageInterface<object>
     */
    public function getStorage($class): StorageInterface
    {
        if ($this->gatewayFactoryRegistry instanceof RegistryInterface) {
            return $this->gatewayFactoryRegistry->getStorage($class);
        }

        throw new \InvalidArgumentException(\sprintf(
            'Storage for given class "%s" does not exist.',
            \is_object($class) ? $class::class : $class
        ));
    }

    public function getStorages(): array
    {
        return $this->gatewayFactoryRegistry->getStorages();
    }

    protected function createGateway(GatewayConfigInterface $gatewayConfig): GatewayInterface
    {
        $config = $gatewayConfig->getConfig();

        if (!isset($config['factory'])) {
            throw new \InvalidArgumentException(\sprintf(
                'Missing "factory" key in gateway config for "%s".',
                $gatewayConfig->getGatewayName()
            ));
        }

        $factory = $this->gatewayFactoryRegistry->getGatewayFactory($config['factory']);
        unset($config['factory']);

        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions($config);

        return $factory->createGateway($containerBuilder->build());
    }
}
