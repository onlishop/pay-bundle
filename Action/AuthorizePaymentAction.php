<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Action;

use Onlishop\Bundle\PayBundle\Exception\RequestNotSupportedException;
use Onlishop\Bundle\PayBundle\GatewayAwareInterface;
use Onlishop\Bundle\PayBundle\GatewayAwareTrait;
use Onlishop\Bundle\PayBundle\Model\Payment;
use Onlishop\Bundle\PayBundle\Model\PaymentInterface;
use Onlishop\Bundle\PayBundle\Request\Authorize;
use Onlishop\Bundle\PayBundle\Request\Convert;
use Onlishop\Bundle\PayBundle\Request\GetHumanStatus;

class AuthorizePaymentAction implements ActionInterface, GatewayAwareInterface
{
    use GatewayAwareTrait;

    /**
     * @param Authorize $request
     */
    public function execute(mixed $request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        /** @var Payment $payment */
        $payment = $request->getModel();

        $this->gateway->execute($status = new GetHumanStatus($payment));
        if ($status->isNew()) {
            $this->gateway->execute($convert = new Convert($payment, 'array'));

            $payment->setDetails($convert->getResult());
        }

        $details = $payment->getDetails();

        $request->setModel($details);
        try {
            $this->gateway->execute($request);
        } finally {
            $payment->setDetails($details);
        }
    }

    public function supports(mixed $request): bool
    {
        return $request instanceof Authorize
            && $request->getModel() instanceof PaymentInterface;
    }
}
