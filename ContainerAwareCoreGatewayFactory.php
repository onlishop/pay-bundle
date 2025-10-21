<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle;

class ContainerAwareCoreGatewayFactory extends CoreGatewayFactory
{
    public function __construct(array $defaultConfig = [])
    {
        parent::__construct($defaultConfig);
    }
}
