<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Tests\Builder;

use Onlishop\Bundle\PayBundle\Builder\GatewayFactoryBuilder;
use Onlishop\Bundle\PayBundle\CoreGatewayFactory;
use Onlishop\Bundle\PayBundle\GatewayFactory;
use PHPUnit\Framework\TestCase;

class GatewayFactoryBuilderTest extends TestCase
{
    public function testShouldBuildContainerAwareCoreGatewayFactory(): void
    {
        $coreGatewayFactory = $this->createMock(CoreGatewayFactory::class);
        $defaultConfig = [
            'foo' => 'fooVal',
        ];

        $builder = new GatewayFactoryBuilder(GatewayFactory::class);

        $gatewayFactory = $builder->build($defaultConfig, $coreGatewayFactory);

        static::assertInstanceOf(GatewayFactory::class, $gatewayFactory);
    }
}
