<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Tests\Action;

use Onlishop\Bundle\PayBundle\Action\CapturePaymentAction;
use Onlishop\Bundle\PayBundle\GatewayAwareInterface;
use Onlishop\Bundle\PayBundle\Model\Payment;
use Onlishop\Bundle\PayBundle\Model\PaymentInterface;
use Onlishop\Bundle\PayBundle\Request\Capture;
use Onlishop\Bundle\PayBundle\Security\TokenInterface;
use PHPUnit\Framework\TestCase;

class CapturePaymentActionTest extends TestCase
{
    protected string $requestClass = Capture::class;

    protected string $actionClass = CapturePaymentAction::class;

    public function provideSupportedRequests(): \Iterator
    {
        $capture = new $this->requestClass($this->createMock(TokenInterface::class));
        $capture->setModel($this->createMock(PaymentInterface::class));
        yield [new $this->requestClass(new Payment())];
        yield [$capture];
    }

    public function testShouldImplementGatewayAwareInterface(): void
    {
        $rc = new \ReflectionClass($this->actionClass);

        static::assertTrue($rc->implementsInterface(GatewayAwareInterface::class));
    }
}
