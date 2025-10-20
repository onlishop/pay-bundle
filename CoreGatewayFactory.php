<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle;

use Onlishop\Bundle\PayBundle\Action\ActionInterface;
use Onlishop\Bundle\PayBundle\Action\AuthorizePaymentAction;
use Onlishop\Bundle\PayBundle\Action\CapturePaymentAction;
use Onlishop\Bundle\PayBundle\Action\ExecuteSameRequestWithModelDetailsAction;
use Onlishop\Bundle\PayBundle\Action\PrependActionInterface;
use Onlishop\Bundle\PayBundle\DI\ContainerConfiguration;
use Onlishop\Bundle\PayBundle\Extension\EndlessCycleDetectorExtension;
use Onlishop\Bundle\PayBundle\Extension\ExtensionInterface;
use Onlishop\Bundle\PayBundle\Extension\PrependExtensionInterface;
use Psr\Container\ContainerInterface;

class CoreGatewayFactory implements ContainerConfiguration, GatewayFactoryConfigInterface
{
    /**
     * @var array<string, mixed>
     */
    protected array $defaultConfig = [];

    /**
     * @param array<string, mixed> $defaultConfig
     */
    public function __construct(array $defaultConfig = [])
    {
        $this->defaultConfig = $defaultConfig;
    }

    public function configureContainer(): array
    {
        return array_merge($this->defaultConfig);
    }

    public function createGateway(
        ContainerInterface $container,
        ?GatewayFactoryConfigInterface $gatewayFactoryConfig = null
    ): Gateway {
        $gateway = new Gateway();

        $allActions = array_merge($this->getActions(), $gatewayFactoryConfig?->getActions() ?? []);
        foreach ($allActions as $action) {
            if (\is_string($action)) {
                $action = $container->get($action);
            }
            $gateway->addAction($action, $action instanceof PrependActionInterface);
        }

        $allExtensions = array_merge($this->getExtensions(), $gatewayFactoryConfig?->getExtensions() ?? []);
        foreach ($allExtensions as $extension) {
            if (\is_string($extension)) {
                $extension = $container->get($extension);
            }
            $gateway->addExtension($extension, $extension instanceof PrependExtensionInterface);
        }

        return $gateway;
    }

    /**
     * @return array<string, class-string<ActionInterface>>|list<class-string<ActionInterface>>
     */
    public function getActions(): array
    {
        return [
            AuthorizePaymentAction::class,
            CapturePaymentAction::class,
            ExecuteSameRequestWithModelDetailsAction::class,
        ];
    }

    /**
     * @return list<ExtensionInterface|class-string<ExtensionInterface>>
     */
    public function getExtensions(): array
    {
        return [
            EndlessCycleDetectorExtension::class,
        ];
    }
}
