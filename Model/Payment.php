<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Model;

class Payment
{
    protected ?string $description = null;

    protected ?string $clientEmail = null;

    protected string $clientId;

    protected ?string $currencyCode = null;

    protected mixed $details = [];

    protected string $number;

    protected int $totalAmount;

    public function setDetails(iterable $details): void
    {
        if ($details instanceof \Traversable) {
            $details = iterator_to_array($details);
        }

        $this->details = $details;
    }

    public function getDetails(): mixed
    {
        return $this->details;
    }

    public function getNumber(): string
    {
        return $this->number;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getClientEmail(): ?string
    {
        return $this->clientEmail;
    }

    public function getClientId(): string
    {
        return $this->clientId;
    }

    public function getTotalAmount(): int
    {
        return $this->totalAmount;
    }

    public function getCurrencyCode(): ?string
    {
        return $this->currencyCode;
    }

    public function setNumber(string $number): void
    {
        $this->number = $number;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function setClientEmail(?string $clientEmail): void
    {
        $this->clientEmail = $clientEmail;
    }

    public function setClientId(string $clientId): void
    {
        $this->clientId = $clientId;
    }

    public function setTotalAmount(int $totalAmount): void
    {
        $this->totalAmount = $totalAmount;
    }

    public function setCurrencyCode(?string $currencyCode): void
    {
        $this->currencyCode = $currencyCode;
    }
}
