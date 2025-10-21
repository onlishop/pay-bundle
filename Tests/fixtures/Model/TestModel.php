<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Tests\fixtures\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 */
class TestModel
{
    public mixed $pay_id = null;

    protected mixed $id = null;

    protected ?int $price = null;

    protected ?string $currency = null;

    public function getPayId(): mixed
    {
        return $this->pay_id;
    }

    public function setPayId(mixed $pay_id): void
    {
        $this->pay_id = $pay_id;
    }

    public function getId(): mixed
    {
        return $this->id;
    }

    public function setId(mixed $id): void
    {
        $this->id = $id;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(?int $price): void
    {
        $this->price = $price;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(?string $currency): void
    {
        $this->currency = $currency;
    }
}
