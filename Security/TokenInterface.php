<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Security;

use Onlishop\Bundle\PayBundle\Model\DetailsAggregateInterface;
use Onlishop\Bundle\PayBundle\Model\DetailsAwareInterface;
use Onlishop\Bundle\PayBundle\Storage\IdentityInterface;

/**
 * @method IdentityInterface getDetails()
 */
interface TokenInterface extends DetailsAggregateInterface, DetailsAwareInterface
{
    public function getHash(): string;

    public function setHash(string $hash): void;

    public function getTargetUrl(): string;

    public function setTargetUrl(string $targetUrl): void;

    public function getAfterUrl(): string;

    public function setAfterUrl(string $afterUrl): void;

    public function getGatewayName(): string;

    public function setGatewayName(string $gatewayName): void;
}
