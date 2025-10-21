<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Tests;

use DI\ContainerBuilder;
use Onlishop\Bundle\PayBundle\CoreGatewayFactory;
use PHPUnit\Framework\TestCase;

class CoreGatewayFactoryTest extends TestCase
{
    public function testShouldAllowCreateGatewayWithoutAnyOptions(): void
    {
        $factory = new CoreGatewayFactory();

        $gateway = $factory->createGateway((new ContainerBuilder())->build());

        static::assertNotNull($gateway);
    }
}
