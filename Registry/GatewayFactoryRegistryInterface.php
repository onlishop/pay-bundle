<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Registry;

use Onlishop\Bundle\PayBundle\DI\ContainerConfiguration;

interface GatewayFactoryRegistryInterface
{
    public function getGatewayFactory(string $name): ContainerConfiguration;

    /**
     * The key must be a gateway factory name
     *
     * @return array<string,ContainerConfiguration>
     */
    public function getGatewayFactories(): array;
}
