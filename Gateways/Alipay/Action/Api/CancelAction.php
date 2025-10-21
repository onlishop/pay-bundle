<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Gateways\Alipay\Action\Api;

use Alipay\OpenAPISDK\Api\AlipayTradeApi;
use Alipay\OpenAPISDK\ApiException;
use Alipay\OpenAPISDK\Model\AlipayTradeCancelModel;
use Alipay\OpenAPISDK\Model\AlipayTradeCloseResponseModel;
use Onlishop\Bundle\PayBundle\Action\ActionInterface;
use Onlishop\Bundle\PayBundle\Bridge\Spl\ArrayObject;
use Onlishop\Bundle\PayBundle\Exception\LogicException;
use Onlishop\Bundle\PayBundle\Exception\RequestNotSupportedException;
use Onlishop\Bundle\PayBundle\Gateways\Alipay\AlipayApi;
use Onlishop\Bundle\PayBundle\Request\Cancel;

class CancelAction implements ActionInterface
{
    public function __construct(protected readonly AlipayApi $alipayApi)
    {
    }

    public function execute(mixed $request): void
    {
          RequestNotSupportedException::assertSupports($this, $request);

        $model = ArrayObject::ensureArrayObject($request->getModel());

        if (empty($model['outTradeNo']) && empty($model['tradeNo'])) {
            throw new LogicException('You must set either "outTradeNo" or "tradeNo".');
        }

        $data = new AlipayTradeCancelModel((array) $model);

        $apiInstance = new AlipayTradeApi(
            alipayConfigUtil: $this->alipayApi->getAlipayConfigUtil(),
        );
        try {
            $result = $apiInstance->cancel($data);
        } catch (ApiException $e) {
            throw new LogicException($e->getResponseBody());
        }

        if ($result instanceof AlipayTradeCloseResponseModel) {
            $model->replace((array) $result->jsonSerialize());
        }
    }

    public function supports(mixed $request): bool
    {
        if (false === ($request instanceof Cancel && $request->getModel() instanceof \ArrayAccess)) {
            return false;
        }

        return true;
    }
}