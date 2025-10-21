<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Gateways\Alipay\Action;

use Onlishop\Bundle\PayBundle\Action\ActionInterface;
use Onlishop\Bundle\PayBundle\Bridge\Spl\ArrayObject;
use Onlishop\Bundle\PayBundle\Exception\RequestNotSupportedException;
use Onlishop\Bundle\PayBundle\Model\PaymentInterface;
use Onlishop\Bundle\PayBundle\Request\Convert;

class ConvertPaymentAction implements ActionInterface
{
    /**
     * @param Convert $request
     */
    public function execute(mixed $request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);
        /** @var PaymentInterface $payment */
        $payment = $request->getSource();

        $details = ArrayObject::ensureArrayObject($payment->getDetails());
        $biz_content = [];
        $biz_content['out_trade_no'] = $payment->getNumber() ?? $details['outTradeNo'];
        $biz_content['subject'] = $details['subject'];
        $biz_content['total_amount'] = number_format($payment->getTotalAmount() / 100, 2, '.', '');

        $details['biz_content'] = array_replace_recursive($biz_content, (array) $details);
        $request->setResult((array) $details);
    }

    public function supports(mixed $request): bool
    {
        return $request instanceof Convert
            && $request->getSource() instanceof PaymentInterface
            && $request->getTo() === 'array';
    }
}
