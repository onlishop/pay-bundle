<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Registry;

use GuzzleHttp\Handler\Proxy;
use Onlishop\Bundle\PayBundle\DI\ContainerConfiguration;
use Onlishop\Bundle\PayBundle\Exception\LogicException;
use Onlishop\Bundle\PayBundle\GatewayInterface;
use Onlishop\Bundle\PayBundle\Storage\StorageInterface;

/**
 * @template StorageType of object
 *
 * @implements RegistryInterface<StorageType>
 */
abstract class AbstractRegistry implements RegistryInterface
{
    /**
     * @param array<class-string<StorageType>, string | StorageInterface<StorageType>> $storages
     * @param array<string, ContainerConfiguration> $gatewayFactories
     * @param array<string, GatewayInterface> $gateways
     */
    public function __construct(
        protected array $gateways = [],
        protected array $storages = [],
        protected array $gatewayFactories = []
    ) {
    }

    public function getGatewayFactory(string $name): ContainerConfiguration
    {
        if (!isset($this->gatewayFactories[$name])) {
            throw new \InvalidArgumentException(\sprintf('Gateway factory "%s" does not exist.', $name));
        }

        $service = $this->getService($this->gatewayFactories[$name]);

        if (!$service instanceof ContainerConfiguration) {
            throw new LogicException(\sprintf(
                'Service "%s" must implement ContainerConfiguration, "%s" given.',
                $this->gatewayFactories[$name],
                get_debug_type($service)
            ));
        }

        return $service;
    }

    /**
     * @return array<string, ContainerConfiguration>
     */
    public function getGatewayFactories(): array
    {
        $gatewayFactories = [];
        foreach (array_keys($this->gatewayFactories) as $name) {
            $gatewayFactories[$name] = $this->getGatewayFactory($name);
        }

        return $gatewayFactories;
    }

    public function getGateway(string $name): GatewayInterface
    {
        if (!isset($this->gateways[$name])) {
            throw new \InvalidArgumentException(\sprintf('Gateway "%s" does not exist.', $name));
        }

        $service = $this->getService($this->gateways[$name]);

        if (!$service instanceof GatewayInterface) {
            throw new \LogicException(\sprintf(
                'Service "%s" must implement GatewayInterface, "%s" given.',
                $this->gateways[$name],
                get_debug_type($service)
            ));
        }

        return $service;
    }

    /**
     * @return array<string, GatewayInterface>
     */
    public function getGateways(): array
    {
        $gateways = [];
        foreach (array_keys($this->gateways) as $name) {
            $gateways[$name] = $this->getGateway($name);
        }

        return $gateways;
    }

    public function getStorage(object|string $class): StorageInterface
    {
        $className = \is_object($class) ? $class::class : $class;

        if (class_exists($className)) {
            $rc = new \ReflectionClass($className);

            if ($rc->implementsInterface(Proxy::class) && $rc->getParentClass() !== false) {
                $className = $rc->getParentClass()->getName();
            }
        }

        if (!isset($this->storages[$className])) {
            throw new \InvalidArgumentException(\sprintf(
                'A storage for model "%s" was not registered. Available storages: %s.',
                $className,
                implode(', ', array_keys($this->storages))
            ));
        }

        $service = $this->getService($this->storages[$className]);

        if (!$service instanceof StorageInterface) {
            throw new \LogicException(\sprintf(
                'Service for "%s" must implement StorageInterface, "%s" given.',
                $className,
                get_debug_type($service)
            ));
        }

        return $service;
    }

    /**
     * @return array<class-string<StorageType>, string | StorageInterface<StorageType>>
     */
    public function getStorages(): array
    {
        return array_map(function ($storageId) {
            return $this->getService($storageId);
        }, $this->storages);
    }

    abstract protected function getService(string|object $id): ?object;
}
