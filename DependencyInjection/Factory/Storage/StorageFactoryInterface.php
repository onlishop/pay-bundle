<?php declare(strict_types=1);

namespace HeyPay\Bundle\PayBundle\DependencyInjection\Factory\Storage;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;

interface StorageFactoryInterface
{
    public function create(ContainerBuilder $container, string $modelClass, array $config): string;

    /**
     * The storage name,
     * For example filesystem, doctrine, propel etc.
     */
    public function getName(): string;

    public function addConfiguration(ArrayNodeDefinition $builder): void;
}
