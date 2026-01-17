<?php

namespace App\VendingMachine\VendingMachines\Application\RetrieveVendingMachine;

use App\Shared\Domain\Bus\Query\Query;

readonly class RetrieveVendingMachineQuery implements Query
{
    public function __construct(
        private string $vendingMachineId
    ) {
    }

    public function vendingMachineId(): string
    {
        return $this->vendingMachineId;
    }
}
