<?php

namespace App\VendingMachine\VendingMachines\Application\UpdateVendingMachine;

use App\Shared\Domain\Bus\Command\Command;

readonly class UpdateVendingMachineCommand implements Command
{
    public function __construct(
        private string $vendingMachineId,
        private array $updatedProducts,
        private array $updatedChangeMoney
    ) {
    }

    public function vendingMachineId(): string
    {
        return $this->vendingMachineId;
    }

    public function updatedProducts(): array
    {
        return $this->updatedProducts;
    }

    public function updatedChangeMoney(): array
    {
        return $this->updatedChangeMoney;
    }
}
