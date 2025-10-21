<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Gateways\Alipay\Action\Api;

use Alipay\OpenAPISDK\Api\AlipayTradeApi;
use Alipay\OpenAPISDK\ApiException;
use Alipay\OpenAPISDK\Model\AlipayTradePrecreateResponseModel;
use Onlishop\Bundle\PayBundle\Action\ActionInterface;
use Onlishop\Bundle\PayBundle\Bridge\Spl\ArrayObject;
use Onlishop\Bundle\PayBundle\Exception\LogicException;
use Onlishop\Bundle\PayBundle\Exception\RequestNotSupportedException;
use Onlishop\Bundle\PayBundle\Gateways\Alipay\AlipayApi;
use Onlishop\Bundle\PayBundle\Request\Capture;

class CaptureQRAction implements ActionInterface
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
        $model['product_code'] = 'QR_CODE_OFFLINE';
        $bizContent = $model['biz_content'];

        if (!isset($bizContent['subject'])) {
            throw new LogicException('The "subject" field is required.');
        }

        $apiInstance = new AlipayTradeApi(alipayConfigUtil: $this->alipayApi->getAlipayConfigUtil());

        try {
            $result = $apiInstance->precreate($bizContent);
            if ($result instanceof AlipayTradePrecreateResponseModel) {
                $model['outTradeNo'] = $result->getOutTradeNo();
                $model['qrCode'] = $result->getQrCode();
                $model['shareCode'] = $result->getShareCode();
            }
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

        return isset($model['payChannel']) && $model['payChannel'] === 'qr';
    }
}