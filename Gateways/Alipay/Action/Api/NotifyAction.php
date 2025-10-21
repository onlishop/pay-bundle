<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Gateways\Alipay\Action\Api;

use Alipay\OpenAPISDK\ApiException;
use Alipay\OpenAPISDK\Util\AlipaySignature;
use Onlishop\Bundle\PayBundle\Action\ActionInterface;
use Onlishop\Bundle\PayBundle\Bridge\Spl\ArrayObject;
use Onlishop\Bundle\PayBundle\Exception\RequestNotSupportedException;
use Onlishop\Bundle\PayBundle\Gateways\Alipay\AlipayApi;
use Onlishop\Bundle\PayBundle\Request\Notify;

class NotifyAction implements ActionInterface
{
    public function __construct(protected readonly AlipayApi $alipayApi)
    {
    }

    /**
     * @param Notify $request
     */
    public function execute(mixed $request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());
        if($model['notify_type'] !== 'trade_status_sync'){
            return;
        }

        try {
            $isValid = AlipaySignature::rsaCheckV1(
                $request->getModel(),
                $this->alipayApi->getAlipayConfig()->getAlipayPublicKey(),
            );

            if (!$isValid) {
                return;
            }




        } catch (ApiException $e) {

        }
    }

    public function supports(mixed $request): bool
    {
        return $request instanceof Notify
            && $request->getModel() instanceof \ArrayAccess;
    }
}