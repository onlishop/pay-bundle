<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Gateways\Alipay\Action;

use Onlishop\Bundle\PayBundle\Action\ActionInterface;
use Onlishop\Bundle\PayBundle\Exception\RequestNotSupportedException;
use Onlishop\Bundle\PayBundle\Model\PaymentInterface;
use Onlishop\Bundle\PayBundle\Request\GetStatusInterface;

class StatusAction implements ActionInterface
{

    /**
     * @param GetStatusInterface $request
     */
    public function execute(mixed $request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $request->markNew();
    }

    public function supports(mixed $request): bool
    {
        return $request instanceof GetStatusInterface
            && (($request->getModel() instanceof PaymentInterface) || ($request->getModel() instanceof \ArrayAccess));
    }
}