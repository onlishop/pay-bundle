<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Registry;

use Onlishop\Bundle\PayBundle\GatewayInterface;

interface GatewayRegistryInterface
{
    public function getGateway(string $name): GatewayInterface;

    /**
     * The key must be a gateway name
     *
     * @return array<string, GatewayInterface>
     */
    public function getGateways(): array;
}
