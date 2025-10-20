<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Storage;

/**
 * @template T of object
 */
interface StorageInterface
{
    /**
     * @return T
     */
    public function create(): object;

    /**
     * @param T $model
     */
    public function support(object $model): bool;

    /**
     * @param T $model
     *
     * @throws \InvalidArgumentException if not supported model given.
     *
     * @return T
     */
    public function update(object $model): object;

    /**
     * @param T $model
     *
     * @throws \InvalidArgumentException if not supported model given.
     */
    public function delete(object $model);

    /**
     * @param mixed|IdentityInterface $id
     *
     * @return ?T
     */
    public function find(mixed $id): ?object;

    /**
     * @return T[]
     */
    public function findBy(array $criteria): array;

    /**
     * @throws \InvalidArgumentException if not supported model given.
     */
    public function identify(object $model): IdentityInterface;
}
