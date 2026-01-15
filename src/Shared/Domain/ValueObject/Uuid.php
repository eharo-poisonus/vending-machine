<?php

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidUuidException;
use Ramsey\Uuid\Uuid as RamseyUuid;

abstract readonly class Uuid
{
    /** @throws InvalidUuidException */
    final public function __construct(protected string $value)
    {
        $this->ensureIsValidUuid();
    }

    abstract public static function fromString(string $value): self;

    /** @throws InvalidUuidException */
    public static function random(): static
    {
        return new static(RamseyUuid::Uuid4()->toString());
    }

    public function value(): string
    {
        return $this->value;
    }

    final public function equals(self $other): bool
    {
        return $this->value === $other->value();
    }

    public function __toString(): string
    {
        return $this->value();
    }

    /** @throws InvalidUuidException */
    private function ensureIsValidUuid(): void
    {
        if (!RamseyUuid::isValid($this->value)) {
            throw new InvalidUuidException(
                sprintf('Invalid uuid: <%s>.', $this->value)
            );
        }
    }
}
