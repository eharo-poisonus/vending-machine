<?php

namespace App\Shared\Domain\ValueObject;

use App\PaymentSessions\Domain\Exception\InvalidDenominationException;

final readonly class Money
{
    private const array ALLOWED_DENOMINATIONS = [5, 10, 25, 1];

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

    /** @throws InvalidDenominationException */
    private function validate(): void
    {
        if (!in_array($this->cents, self::ALLOWED_DENOMINATIONS, true)) {
            throw new InvalidDenominationException();
        }
    }
}
