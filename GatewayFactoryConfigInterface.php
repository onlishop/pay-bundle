<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle;

use Onlishop\Bundle\PayBundle\Action\ActionInterface;
use Onlishop\Bundle\PayBundle\Extension\ExtensionInterface;
use Psr\Container\ContainerInterface;

interface GatewayFactoryConfigInterface
{
    public function createGateway(ContainerInterface $container): Gateway;

    /**
     * @return array<string, class-string<ActionInterface>>|list<class-string<ActionInterface>>
     */
    public function getActions(): array;

    /**
     * @return list<ExtensionInterface|class-string<ExtensionInterface>>
     */
    public function getExtensions(): array;
}
