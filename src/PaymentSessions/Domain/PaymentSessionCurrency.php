<?php

namespace App\PaymentSessions\Domain;

use App\Shared\Domain\ValueObject\Money;

class PaymentSessionCurrency
{
    private function __construct(
        private Money $denomination,
        private int $amount = 1
    ) {
    }

    public static function create(Money $denomination): self
    {
        return new self($denomination);
    }

    public function denomination(): Money
    {
        return $this->denomination;
    }

    public function setDenomination(Money $denomination): void
    {
        $this->denomination = $denomination;
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function addCurrency(): void
    {
        $this->amount++;
    }
}
