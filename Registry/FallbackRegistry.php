<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Registry;

use Onlishop\Bundle\PayBundle\DI\ContainerConfiguration;
use Onlishop\Bundle\PayBundle\GatewayInterface;
use Onlishop\Bundle\PayBundle\Storage\StorageInterface;

/**
 * @template T of object
 *
 * @implements RegistryInterface<T>
 */
class FallbackRegistry implements RegistryInterface
{
    /**
     * @var RegistryInterface<T>
     */
    private RegistryInterface $registry;

    /**
     * @var RegistryInterface<T>
     */
    private RegistryInterface $fallbackRegistry;

    /**
     * @param RegistryInterface<T> $registry
     * @param RegistryInterface<T> $fallbackRegistry
     */
    public function __construct(RegistryInterface $registry, RegistryInterface $fallbackRegistry)
    {
        $this->registry = $registry;
        $this->fallbackRegistry = $fallbackRegistry;
    }

    public function getGatewayFactory(string $name): ContainerConfiguration
    {
        try {
            return $this->registry->getGatewayFactory($name);
        } catch (\InvalidArgumentException) {
            return $this->fallbackRegistry->getGatewayFactory($name);
        }
    }

    /**
     * @return ContainerConfiguration[]
     */
    public function getGatewayFactories(): array
    {
        return array_replace($this->fallbackRegistry->getGatewayFactories(), $this->registry->getGatewayFactories());
    }

    public function getGateway(string $name): GatewayInterface
    {
        try {
            return $this->registry->getGateway($name);
        } catch (\InvalidArgumentException) {
            return $this->fallbackRegistry->getGateway($name);
        }
    }

    public function getGateways(): array
    {
        return array_replace($this->fallbackRegistry->getGateways(), $this->registry->getGateways());
    }

    /**
     * @param class-string|T $class
     *
     * @return StorageInterface<T>
     */
    public function getStorage(string|object $class): StorageInterface
    {
        try {
            return $this->registry->getStorage($class);
        } catch (\InvalidArgumentException) {
            return $this->fallbackRegistry->getStorage($class);
        }
    }

    /**
     * @return array<class-string, T>>
     */
    public function getStorages(): array
    {
        return array_replace($this->fallbackRegistry->getStorages(), $this->registry->getStorages());
    }
}
