<?php

namespace App\Shared\Domain\ValueObject;

use App\VendingMachine\PaymentSessions\Domain\Exception\InvalidDenominationException;

final readonly class Money
{
    /** @throws InvalidDenominationException */
    public function __construct(
        private int $cents
    ) {
        $this->validate();
    }

    /** @throws InvalidDenominationException */
    public static function fromFloat(float $value): self
    {
        $cents = (int)round($value * 100);
        return new self($cents);
    }

    /** @throws InvalidDenominationException */
    public static function fromCents(int $cents): self
    {
        return new self($cents);
    }

    public function cents(): int
    {
        return $this->cents;
    }

    public function value(): float
    {
        return $this->cents / 100;
    }

    public function greaterOrEqualThan(Money $other): bool
    {
        return $this->cents >= $other->cents();
    }

    /** @throws InvalidDenominationException */
    private function validate(): void
    {
        if ($this->cents() <= 0) {
            throw new InvalidDenominationException();
        }
    }
}
