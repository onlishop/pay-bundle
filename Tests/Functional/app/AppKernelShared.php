<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Tests\Functional\app;

use Onlishop\Bundle\PayBundle\PayBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Kernel;

class AppKernelShared extends Kernel
{
    public function registerBundles(): iterable
    {
        return [
            new FrameworkBundle(),
            new PayBundle(),
        ];
    }

    public function getLogDir(): string
    {
        return sys_get_temp_dir() . '/HeyPayBundle/logs';
    }

    public function getCacheDir(): string
    {
        return sys_get_temp_dir() . '/HeyPayBundle/cache';
    }

    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(__DIR__ . '/config/config.yml');
    }
}
