<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle;

trait GatewayAwareTrait
{
    protected GatewayInterface $gateway;

    public function setGateway(GatewayInterface $gateway): void
    {
        $this->gateway = $gateway;
    }
}
