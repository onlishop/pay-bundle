<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\DI;

use Onlishop\Bundle\PayBundle\Gateway;
use Psr\Container\ContainerInterface;

interface ContainerConfiguration
{
    /**
     * @return array<string, mixed>
     */
    public function configureContainer(): array;

    public function createGateway(ContainerInterface $container): Gateway;
}
