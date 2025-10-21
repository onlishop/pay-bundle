<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Gateways\Alipay\Action\Api;

use Alipay\OpenAPISDK\Api\AlipayTradeApi;
use Alipay\OpenAPISDK\ApiException;
use Alipay\OpenAPISDK\Model\AlipayTradeQueryModel;
use Alipay\OpenAPISDK\Model\AlipayTradeQueryResponseModel;
use Onlishop\Bundle\PayBundle\Action\ActionInterface;
use Onlishop\Bundle\PayBundle\Bridge\Spl\ArrayObject;
use Onlishop\Bundle\PayBundle\Exception\LogicException;
use Onlishop\Bundle\PayBundle\Exception\RequestNotSupportedException;
use Onlishop\Bundle\PayBundle\Gateways\Alipay\AlipayApi;
use Onlishop\Bundle\PayBundle\Request\Query;

class QueryAction implements ActionInterface
{
    public function __construct(protected readonly AlipayApi $alipayApi)
    {
    }

    /**
     * @param Query $request
     */
    public function execute(mixed $request): void
    {
        RequestNotSupportedException::assertSupports($this, $request);

        $details = ArrayObject::ensureArrayObject($request->getModel());

        if (empty($details['outTradeNo']) && empty($details['tradeNo'])) {
            throw new LogicException('You must set either "outTradeNo" or "tradeNo".');
        }

        $data = new AlipayTradeQueryModel((array)$details);

        $apiInstance = new AlipayTradeApi(
            alipayConfigUtil: $this->alipayApi->getAlipayConfigUtil(),
        );

        if (isset($details['queryOptions'])) {
            $data->setQueryOptions($details['queryOptions']);
        }

        try {
            $response = $apiInstance->query($data);
        } catch (ApiException $e) {
            throw new LogicException($e->getResponseBody());
        }

        if ($response instanceof AlipayTradeQueryResponseModel) {
            $details->replace((array)$response->jsonSerialize());
        }
    }

    public function supports(mixed $request): bool
    {
        return $request instanceof Query
            && $request->getModel() instanceof \ArrayAccess;
    }


}