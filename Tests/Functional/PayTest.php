<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Tests\Functional;

use Onlishop\Bundle\PayBundle\Pay;

class PayTest extends WebTestCase
{
    public function testCouldBeGetFromContainerAsService(): void
    {
        /** @var Pay $pay */
        $pay = static::getContainer()->get('pay');

        static::assertInstanceOf(Pay::class, $pay);
    }
}
