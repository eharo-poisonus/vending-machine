<?php

namespace App\VendingMachine\VendingMachines\Application\RetrieveVendingMachine;

use App\Shared\Domain\Bus\Query\Response;
use App\VendingMachine\VendingMachines\Domain\MachineChangeCurrency;

readonly class MachineChangeResponse implements Response
{
    public function __construct(
        private float $value,
        private int $amount
    ) {
    }

    public static function fromMachineChangeCurrency(MachineChangeCurrency $machineChangeCurrency): self
    {
        return new self(
            $machineChangeCurrency->denomination()->money()->value(),
            $machineChangeCurrency->amount()
        );
    }

    public function value(): float
    {
        return $this->value;
    }

    public function amount(): int
    {
        return $this->amount;
    }
}
