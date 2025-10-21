<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Gateways\Alipay;

use Onlishop\Bundle\PayBundle\Bridge\Spl\ArrayObject;
use Onlishop\Bundle\PayBundle\GatewayFactory;

class AlipayGatewayFactory extends GatewayFactory
{
    protected function populateConfig(ArrayObject $config): void
    {
        parent::populateConfig($config);
    }
}