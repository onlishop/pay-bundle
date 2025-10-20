<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Model;

interface GatewayConfigInterface
{
    public function getGatewayName(): string;

    public function setGatewayName(string $gatewayName): void;

    /**
     * @param array<string,mixed> $config
     */
    public function setConfig(array $config): void;

    /**
     * @return array<string,mixed>
     */
    public function getConfig(): array;
}
