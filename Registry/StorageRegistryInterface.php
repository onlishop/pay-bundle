<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Registry;

use Onlishop\Bundle\PayBundle\Storage\StorageInterface;

/**
 * @template T of object
 */
interface StorageRegistryInterface
{
    /**
     * @param class-string|T $class
     *
     * @throws \InvalidArgumentException if storage with such name not exists
     *
     * @return StorageInterface<T>
     */
    public function getStorage(string|object $class): StorageInterface;

    /**
     * The key must be a model class
     *
     * @return array<class-string, StorageInterface<T>>
     */
    public function getStorages(): array;
}
