<?php

namespace App\VendingMachine\Shared\Domain;

use App\Shared\Domain\ValueObject\Money;

class CurrencyDenomination
{
    public function __construct(
        private int $id,
        private Money $money
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function money(): Money
    {
        return $this->money;
    }

    public function setMoney(Money $money): void
    {
        $this->money = $money;
    }
}
