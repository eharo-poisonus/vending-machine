<?php

namespace App\VendingMachine\VendingMachines\Application\RetrieveVendingMachine;

use App\Shared\Domain\Bus\Query\Response;
use App\VendingMachine\VendingMachines\Domain\MachineChangeCurrency;

readonly class MachineChangeResponse implements Response
{
    public function __construct(
        private int $id,
        private float $value,
        private int $amount
    ) {
    }

    public static function fromMachineChangeCurrency(MachineChangeCurrency $machineChangeCurrency): self
    {
        return new self(
            $machineChangeCurrency->id(),
            $machineChangeCurrency->denomination()->money()->value(),
            $machineChangeCurrency->amount()
        );
    }

    public function id(): int
    {
        return $this->id;
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
