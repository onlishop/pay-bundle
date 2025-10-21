<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Tests;

use Onlishop\Bundle\PayBundle\DI\ContainerConfiguration;
use Onlishop\Bundle\PayBundle\GatewayFactory;
use PHPUnit\Framework\TestCase;

class GatewayFactoryTest extends TestCase
{
    public function testShouldImplementGatewayFactoryInterface(): void
    {
        $rc = new \ReflectionClass(GatewayFactory::class);

        static::assertTrue($rc->implementsInterface(ContainerConfiguration::class));
    }
}
