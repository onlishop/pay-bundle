<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Registry;
/**
 * @template T of object
 *
 * @extends StorageRegistryInterface<T>
 */
interface RegistryInterface extends GatewayRegistryInterface, GatewayFactoryRegistryInterface,StorageRegistryInterface
{
}
