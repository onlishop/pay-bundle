<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Request;

use Onlishop\Bundle\PayBundle\Model\ModelAggregateInterface;
use Onlishop\Bundle\PayBundle\Model\ModelAwareInterface;

interface GetStatusInterface extends ModelAwareInterface, ModelAggregateInterface
{
    public function getValue(): mixed;

    public function markNew(): void;

    public function isNew(): bool;

    public function markCaptured(): void;

    public function isCaptured(): bool;

    public function isAuthorized(): bool;

    public function markAuthorized(): void;

    public function markPayedout(): void;

    public function isPayedout(): bool;

    public function isRefunded(): bool;

    public function markRefunded(): void;

    public function isSuspended(): bool;

    public function markSuspended(): void;

    public function isExpired(): bool;

    public function markExpired(): void;

    public function isCanceled(): bool;

    public function markCanceled(): void;

    public function isPending(): bool;

    public function markPending(): void;

    public function isFailed(): bool;

    public function markFailed(): void;

    public function isUnknown(): bool;

    public function markUnknown(): void;
}
