<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Model;

class Payout implements PayoutInterface
{
    protected string $recipientId;

    protected string $recipientEmail;

    protected int $totalAmount;

    protected string $currencyCode;

    protected string $description;

    /**
     * @var array<string,mixed>
     */
    protected array $details = [];

    public function __construct()
    {
    }

    public function getRecipientId(): string
    {
        return $this->recipientId;
    }

    public function setRecipientId(string $recipientId): void
    {
        $this->recipientId = $recipientId;
    }

    public function getRecipientEmail(): string
    {
        return $this->recipientEmail;
    }

    public function setRecipientEmail(string $recipientEmail): void
    {
        $this->recipientEmail = $recipientEmail;
    }

    public function getTotalAmount(): int
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(int $totalAmount): void
    {
        $this->totalAmount = $totalAmount;
    }

    public function getCurrencyCode(): string
    {
        return $this->currencyCode;
    }

    public function setCurrencyCode(string $currencyCode): void
    {
        $this->currencyCode = $currencyCode;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getDetails(): array
    {
        return $this->details;
    }

    public function setDetails(object $details): void
    {
        if ($details instanceof \Traversable) {
            $details = iterator_to_array($details);
        }

        $this->details = $details;
    }
}
