<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle;

use Onlishop\Bundle\PayBundle\Bridge\Spl\ArrayObject;
use Onlishop\Bundle\PayBundle\DI\ContainerConfiguration;
use Psr\Container\ContainerInterface;

class GatewayFactory implements ContainerConfiguration, GatewayFactoryConfigInterface
{
    protected CoreGatewayFactory $coreGatewayFactory;

    /**
     * @var array<string, mixed>
     */
    protected array $defaultConfig = [];

    /**
     * @param array<string, mixed> $defaultConfig
     */
    public function __construct(array $defaultConfig = [], ?CoreGatewayFactory $coreGatewayFactory = null)
    {
        $this->coreGatewayFactory = $coreGatewayFactory ?: new CoreGatewayFactory();
        $this->defaultConfig = $defaultConfig;
    }

    public function configureContainer(): array
    {
        $config = ArrayObject::ensureArrayObject($this->defaultConfig);
        $config->defaults($this->coreGatewayFactory->configureContainer());
        $this->populateConfig($config);

        return (array) $config;
    }

    public function createGateway(ContainerInterface $container): Gateway
    {
        return $this->coreGatewayFactory->createGateway($container);
    }

    public function getActions(): array
    {
        return [];
    }

    public function getExtensions(): array
    {
        return [];
    }

    protected function populateConfig(ArrayObject $config): void
    {
    }
}
