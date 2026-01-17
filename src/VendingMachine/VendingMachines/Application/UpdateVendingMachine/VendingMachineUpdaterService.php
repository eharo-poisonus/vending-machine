<?php

namespace App\VendingMachine\VendingMachines\Application\UpdateVendingMachine;

use App\VendingMachine\VendingMachines\Domain\Exception\CurrencyAmountAddedCanNotBeNegativeException;
use App\VendingMachine\VendingMachines\Domain\Exception\ProductStockAddedCanNotBeNegativeException;
use App\VendingMachine\VendingMachines\Domain\Exception\VendingMachineDoesNotExistException;
use App\VendingMachine\VendingMachines\Domain\Product;
use App\VendingMachine\VendingMachines\Domain\VendingMachine;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;
use App\VendingMachine\VendingMachines\Domain\VendingMachineRepository;

final readonly class VendingMachineUpdaterService
{
    public function __construct(
        private VendingMachineRepository $vendingMachineRepository
    ) {
    }

    /** @throws VendingMachineDoesNotExistException|ProductStockAddedCanNotBeNegativeException|CurrencyAmountAddedCanNotBeNegativeException */
    public function __invoke(
        VendingMachineId $vendingMachineId,
        array $addedProducts,
        array $addedChangeCurrencies
    ): void {
        $vendingMachine = $this->vendingMachineRepository->id($vendingMachineId);
        $this->ensureVendingMachineExists($vendingMachine);

        $vendingMachine->addStockToProducts($addedProducts);
        $vendingMachine->addChangeCurrenciesToMoneyInventory($addedChangeCurrencies);

        $this->vendingMachineRepository->update($vendingMachine);
    }

    /** @throws VendingMachineDoesNotExistException */
    private function ensureVendingMachineExists(?VendingMachine $vendingMachine): void
    {
        if ($vendingMachine === null) {
            throw new VendingMachineDoesNotExistException();
        }
    }
}
