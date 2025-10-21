<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Model;

use Onlishop\Bundle\PayBundle\Storage\IdentityInterface;

class Identity implements IdentityInterface, \Stringable
{
    public function __construct(
        protected mixed $id,
        protected string|object $class
    ) {
        $this->class = \is_object($class) ? $class::class : $class;
    }

    public function __serialize(): array
    {
        return [$this->id, $this->class];
    }

    public function __unserialize(array $data): void
    {
        [$this->id, $this->class] = $data;
    }

    public function __toString(): string
    {
        return $this->class . '#' . $this->id;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getId(): mixed
    {
        return $this->id;
    }

    public function serialize(): ?string
    {
        return serialize([$this->id, $this->class]);
    }

    public function unserialize(string $data): void
    {
        [$this->id, $this->class] = unserialize($data);
    }
}
