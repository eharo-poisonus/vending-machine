<?php

namespace App\VendingMachine\VendingMachines\Domain;

use App\VendingMachine\Shared\Domain\CurrencyDenomination;
use App\VendingMachine\VendingMachines\Domain\Exception\CurrencyAmountAddedCanNotBeNegativeException;

class MachineChangeCurrency
{
    public function __construct(
        private int $id,
        private VendingMachine $vendingMachine,
        private CurrencyDenomination $denomination,
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

    public function denomination(): CurrencyDenomination
    {
        return $this->denomination;
    }

    public function setDenomination(CurrencyDenomination $denomination): void
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

    /** @throws CurrencyAmountAddedCanNotBeNegativeException */
    public function addAmount(int $amountToAdd): void
    {
        if ($amountToAdd < 0) {
            throw new CurrencyAmountAddedCanNotBeNegativeException();
        }

        $this->amount += $amountToAdd;
    }

    public function removeAmount(int $amountToRemove): void
    {
        $this->amount -= $amountToRemove;
    }
}
