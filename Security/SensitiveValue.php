<?php declare(strict_types=1);

namespace Onlishop\Bundle\PayBundle\Security;

use Onlishop\Bundle\PayBundle\Exception\LogicException;
use Onlishop\Bundle\PayBundle\Security\Util\Mask;

final class SensitiveValue implements \Serializable, \JsonSerializable, \Stringable
{
    public function __construct(protected mixed $value)
    {
    }

    public function __serialize(): array
    {
        return [];
    }

    public function __unserialize(array $data): void
    {
        $this->value = null;
    }

    public function __toString(): string
    {
        return '';
    }

    public function __clone()
    {
        throw new LogicException('It is not permitted to close this object.');
    }

    public function __debugInfo()
    {
        return [
            'value' => \is_scalar($this->value) ? Mask::mask($this->value) : '[FILTERED OUT]',
        ];
    }

    public function peek(): mixed
    {
        return $this->value;
    }

    public function get(): mixed
    {
        $value = $this->value;

        $this->erase();

        return $value;
    }

    public function erase(): void
    {
        $this->value = null;
    }

    public function serialize(): ?string
    {
        return null;
    }

    public function unserialize(string $data): void
    {
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): void
    {
    }

    public static function ensureSensitive(mixed $value): SensitiveValue
    {
        return $value instanceof self ? $value : new self($value);
    }
}
