<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Builder;

use Onlishop\Bundle\PayBundle\DI\ContainerConfiguration;

class GatewayFactoryBuilder
{
    public function __construct(private readonly string $gatewayFactoryClass)
    {
    }

    public function __invoke()
    {
        return \call_user_func_array([$this, 'build'], \func_get_args());
    }

    public function build(array $defaultConfig, ContainerConfiguration $coreGatewayFactory): ContainerConfiguration
    {
        $gatewayFactoryClass = $this->gatewayFactoryClass;

        return new $gatewayFactoryClass($defaultConfig, $coreGatewayFactory);
    }
}
