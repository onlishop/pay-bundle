<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Registry;

use Onlishop\Bundle\PayBundle\DI\ContainerConfiguration;
use Onlishop\Bundle\PayBundle\GatewayInterface;
use Onlishop\Bundle\PayBundle\PayException;

class FallbackRegistry implements RegistryInterface
{
    public function __construct(protected readonly RegistryInterface $registry, protected readonly RegistryInterface $fallbackRegistry)
    {
    }

    public function getGatewayFactory(string $name): ContainerConfiguration
    {
        try {
            return $this->registry->getGatewayFactory($name);
        } catch (PayException) {
            return $this->fallbackRegistry->getGatewayFactory($name);
        }
    }

    public function getGatewayFactories(): array
    {
        return array_replace($this->fallbackRegistry->getGatewayFactories(), $this->registry->getGatewayFactories());
    }

    public function getGateway(string $name): GatewayInterface
    {
        try {
            return $this->registry->getGateway($name);
        } catch (PayException) {
            return $this->fallbackRegistry->getGateway($name);
        }
    }

    public function getGateways(): array
    {
        return array_replace($this->fallbackRegistry->getGateways(), $this->registry->getGateways());
    }
}
