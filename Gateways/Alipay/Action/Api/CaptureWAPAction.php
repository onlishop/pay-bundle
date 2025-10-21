<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Gateways\Alipay\Action\Api;

use Alipay\OpenAPISDK\ApiException;
use Alipay\OpenAPISDK\Util\GenericExecuteApi;
use Onlishop\Bundle\PayBundle\Action\ActionInterface;
use Onlishop\Bundle\PayBundle\Bridge\Spl\ArrayObject;
use Onlishop\Bundle\PayBundle\Exception\LogicException;
use Onlishop\Bundle\PayBundle\Exception\RequestNotSupportedException;
use Onlishop\Bundle\PayBundle\Gateways\Alipay\AlipayApi;
use Onlishop\Bundle\PayBundle\Request\Capture;

class CaptureWAPAction implements ActionInterface
{
    public function __construct(protected readonly AlipayApi $alipayApi)
    {
    }

    /**
     * @param Capture $request
     */
    public function execute(mixed $request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());
        $bizContent = $model['biz_content'];
        if (!isset($bizContent['subject'])) {
            throw new LogicException('The "subject" field is required.');
        }
        $model['product_code'] = 'QUICK_WAP_WAY';

        $apiInstance = new GenericExecuteApi(alipayConfigUtil: $this->alipayApi->getAlipayConfigUtil());
        try {
            $model['payJumpUrl'] = $apiInstance->pageExecute('alipay.trade.wap.pay', 'POST', $model['biz_content']);
        } catch (ApiException $e) {
            echo $e->getMessage();
        }
    }

    public function supports(mixed $request): bool
    {
        if (!$request instanceof Capture) {
            return false;
        }

        $model = $request->getModel();
        if (!$model instanceof \ArrayAccess) {
            return false;
        }

        return isset($model['payChannel']) && $model['payChannel'] === 'wap';
    }
}