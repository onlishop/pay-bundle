<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Registry;

/**
 * @template T of object
 *
 * @extends AbstractRegistry<T>
 */
class SimpleRegistry extends AbstractRegistry
{
    protected function getService(object|string $id): ?object
    {
        return $id;
    }
}
