<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle;

use Onlishop\Bundle\PayBundle\DependencyInjection\Compiler\BuildConfigsPass;
use Onlishop\Bundle\PayBundle\DependencyInjection\Compiler\BuildGatewayFactoriesBuilderPass;
use Onlishop\Bundle\PayBundle\DependencyInjection\Compiler\BuildGatewayFactoriesPass;
use Onlishop\Bundle\PayBundle\DependencyInjection\Compiler\BuildGatewaysPass;
use Onlishop\Bundle\PayBundle\DependencyInjection\Compiler\BuildStoragesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class PayBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);
        $container->addCompilerPass(new BuildConfigsPass());
        $container->addCompilerPass(new BuildGatewaysPass());
        $container->addCompilerPass(new BuildStoragesPass());
        $container->addCompilerPass(new BuildGatewayFactoriesPass());
        $container->addCompilerPass(new BuildGatewayFactoriesBuilderPass());
    }
}
