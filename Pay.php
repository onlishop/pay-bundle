<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle;

use Onlishop\Bundle\PayBundle\DI\ContainerConfiguration;
use Onlishop\Bundle\PayBundle\Registry\RegistryInterface;
use Onlishop\Bundle\PayBundle\Security\TokenInterface;
use Onlishop\Bundle\PayBundle\Storage\StorageInterface;

/**
 * @template StorageType of object
 *
 * @implements RegistryInterface<StorageType>
 */
class Pay implements RegistryInterface
{
    /**
     * @param RegistryInterface<StorageType> $registry
     */
    public function __construct(
        protected RegistryInterface $registry,
        protected StorageInterface $tokenStorage
    ) {
    }

    public function getGatewayFactory(string $name): ContainerConfiguration
    {
        return $this->registry->getGatewayFactory($name);
    }

    /**
     * @return StorageInterface<TokenInterface>
     */
    public function getTokenStorage(): StorageInterface
    {
        return $this->tokenStorage;
    }

    public function getGatewayFactories(): array
    {
        return $this->registry->getGatewayFactories();
    }

    public function getGateway(string $name): GatewayInterface
    {
        return $this->registry->getGateway($name);
    }

    public function getGateways(): array
    {
        return $this->registry->getGateways();
    }

    /**
     * @return StorageInterface<StorageType>
     */
    public function getStorage(object|string $class): StorageInterface
    {
        return $this->registry->getStorage($class);
    }

    public function getStorages(): array
    {
        return $this->registry->getStorages();
    }
}
