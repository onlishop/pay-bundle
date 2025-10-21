<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Model;

/**
 * @method array getDetails()
 */
interface PayoutInterface extends DetailsAggregateInterface, DetailsAwareInterface
{
    public function getRecipientId(): string;

    public function getRecipientEmail(): string;

    public function getDescription(): string;

    public function getTotalAmount(): int;

    public function getCurrencyCode(): string;
}
