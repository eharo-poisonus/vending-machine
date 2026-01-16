<?php

namespace App\VendingMachine\PaymentSessions\Application\InsertMoney;

use App\Shared\Domain\Bus\Command\Command;

readonly class InsertMoneyCommand implements Command
{
    public function __construct(
        private string $vendingMachineId,
        private string $paymentSessionId,
        private float $insertedMoney
    ) {
    }

    public function vendingMachineId(): string
    {
        return $this->vendingMachineId;
    }

    public function paymentSessionId(): string
    {
        return $this->paymentSessionId;
    }

    public function insertedMoney(): float
    {
        return $this->insertedMoney;
    }
}
