<?php declare(strict_types=1);

namespace HeyPay\Bundle\PayBundle\DependencyInjection\Factory\Storage;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

abstract class AbstractStorageFactory implements StorageFactoryInterface
{
    public function create(ContainerBuilder $container, string $modelClass, array $config): string
    {
        $storageId = \sprintf('pay.storage.%s', strtolower(str_replace(['\\\\', '\\'], '_', $modelClass)));

        $storageDefinition = $this->createStorage($container, $modelClass, $config);
        $storageDefinition->setPublic(true);

        $container->setDefinition($storageId, $storageDefinition);

        return $storageId;
    }

    public function addConfiguration(ArrayNodeDefinition $builder): void
    {
    }

    abstract protected function createStorage(ContainerBuilder $container, string $modelClass, array $config): Definition;
}
