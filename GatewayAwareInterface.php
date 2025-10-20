<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle;

interface GatewayAwareInterface
{
    public function setGateway(GatewayInterface $gateway): void;
}
