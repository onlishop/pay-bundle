<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Request;

class Convert
{
    protected mixed $result;

    public function __construct(
        protected mixed $source,
        protected string $to,
    ) {
    }

    public function getSource(): mixed
    {
        return $this->source;
    }

    public function getTo(): string
    {
        return $this->to;
    }

    public function getResult(): mixed
    {
        return $this->result;
    }

    public function setResult(mixed $result): void
    {
        $this->result = $result;
    }
}
