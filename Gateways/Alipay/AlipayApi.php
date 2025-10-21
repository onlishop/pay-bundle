<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Gateways\Alipay;

use Alipay\OpenAPISDK\Util\AlipayConfigUtil;
use Alipay\OpenAPISDK\Util\Model\AlipayConfig;

class AlipayApi
{
    protected AlipayConfig $alipayConfig;
    protected AlipayConfigUtil $alipayConfigUtil;
    /**
     * @param array<string,mixed> $config
     */
    public function __construct(array $config)
    {
        $this->alipayConfig = new AlipayConfig();
        $this->alipayConfig->setAppId($config['appId']);
        $this->alipayConfig->setPrivateKey($config['privateKey']);
        $this->alipayConfig->setAlipayPublicKey($config['alipayPublicKey']);
        $this->alipayConfig->setServerUrl($config['sandbox'] ?  'https://openapi-sandbox.dl.alipaydev.com' : 'https://openapi.alipay.com');

        $this->alipayConfigUtil = new AlipayConfigUtil($this->alipayConfig);
    }

    public function getAlipayConfig(): AlipayConfig
    {
        return $this->alipayConfig;
    }

    public function getAlipayConfigUtil(): AlipayConfigUtil
    {
        return $this->alipayConfigUtil;
    }


}