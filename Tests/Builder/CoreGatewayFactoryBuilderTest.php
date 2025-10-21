<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Tests\Builder;

use Onlishop\Bundle\PayBundle\Builder\CoreGatewayFactoryBuilder;
use Onlishop\Bundle\PayBundle\ContainerAwareCoreGatewayFactory;
use PHPUnit\Framework\TestCase;

class CoreGatewayFactoryBuilderTest extends TestCase
{
    public function testAllowUseBuilderAsAsFunction(): void
    {
        $defaultConfig = [
            'foo' => 'fooVal',
        ];

        $builder = new CoreGatewayFactoryBuilder();

        $gatewayFactory = $builder($defaultConfig);

        static::assertInstanceOf(ContainerAwareCoreGatewayFactory::class, $gatewayFactory);
    }
}
