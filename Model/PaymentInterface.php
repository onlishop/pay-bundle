<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Model;

/**
 * @method array getDetails()
 */
interface PaymentInterface extends DetailsAggregateInterface, DetailsAwareInterface
{
    public function getNumber(): string;

    public function getDescription(): ?string;

    public function getClientEmail(): ?string;

    public function getClientId(): string;

    public function getTotalAmount(): int;

    public function getCurrencyCode(): ?string;
}
