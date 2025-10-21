<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Gateways\Alipay;

use DI\Container;
use Onlishop\Bundle\PayBundle\Action\PrependActionInterface;
use Onlishop\Bundle\PayBundle\Bridge\Spl\ArrayObject;
use Onlishop\Bundle\PayBundle\Gateway;
use Onlishop\Bundle\PayBundle\GatewayFactory;
use Onlishop\Bundle\PayBundle\GatewayFactoryConfigInterface;
use Onlishop\Bundle\PayBundle\Gateways\Alipay\Action\Api\CancelAction;
use Onlishop\Bundle\PayBundle\Gateways\Alipay\Action\Api\CapturePCAction;
use Onlishop\Bundle\PayBundle\Gateways\Alipay\Action\Api\CaptureQRAction;
use Onlishop\Bundle\PayBundle\Gateways\Alipay\Action\Api\CaptureWAPAction;
use Onlishop\Bundle\PayBundle\Gateways\Alipay\Action\Api\CloseAction;
use Onlishop\Bundle\PayBundle\Gateways\Alipay\Action\Api\NotifyAction;
use Onlishop\Bundle\PayBundle\Gateways\Alipay\Action\Api\QueryAction;
use Onlishop\Bundle\PayBundle\Gateways\Alipay\Action\ConvertPaymentAction;
use Onlishop\Bundle\PayBundle\Gateways\Alipay\Action\StatusAction;
use Psr\Container\ContainerInterface;

class AlipayGatewayFactory extends GatewayFactory implements GatewayFactoryConfigInterface
{
    protected function populateConfig(ArrayObject $config): void
    {
        $config->defaults([
            AlipayApi::class => function (Container $container) {
                return new AlipayApi([
                    'sandbox' => $container->get('sandbox'),
                    'appId' => $container->get('app_id'),
                    'privateKey' => $container->get('private_key'),
                    'alipayPublicKey' => $container->get('alipay_public_key'),
                ]);
            }
        ]);
    }

    public function getActions(): array
    {
        return [
            StatusAction::class,
            ConvertPaymentAction::class,
            CapturePCAction::class,
            CaptureQRAction::class,
            CancelAction::class,
            CloseAction::class,
            QueryAction::class,
            NotifyAction::class,
            CaptureWAPAction::class,
        ];
    }

    public function createGateway(ContainerInterface $container): Gateway
    {
        $gateway = parent::createGateway($container);
        foreach ($this->getActions() as $action) {
            if (\is_string($action)) {
                $action = $container->get($action);
            }
            $gateway->addAction($action, $action instanceof PrependActionInterface);
        }

        return $gateway;
    }

    public function getExtensions(): array
    {
        return [];
    }
}
