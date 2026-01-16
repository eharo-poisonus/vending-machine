<?php

namespace App\VendingMachine\VendingMachines\Domain;

use App\Shared\Domain\ValueObject\Money;

class MachineChangeCurrency
{
    public function __construct(
        private int $id,
        private VendingMachine $vendingMachine,
        private Money $denomination,
        private int $amount
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

    public function vendingMachine(): VendingMachine
    {
        return $this->vendingMachine;
    }

    public function setVendingMachine(VendingMachine $vendingMachine): void
    {
        $this->vendingMachine = $vendingMachine;
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
}
