<?php

namespace App\VendingMachine\VendingMachines\Application\RetrieveVendingMachine;

use App\VendingMachine\VendingMachines\Domain\Exception\VendingMachineDoesNotExistException;
use App\VendingMachine\VendingMachines\Domain\VendingMachine;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;
use App\VendingMachine\VendingMachines\Domain\VendingMachineRepository;

final readonly class VendingMachineRetrieverService
{
    public function __construct(
        private VendingMachineRepository $vendingMachineRepository
    ) {
    }

    /** @throws VendingMachineDoesNotExistException */
    public function __invoke(VendingMachineId $vendingMachineId): VendingMachine
    {
        $vendingMachine = $this->vendingMachineRepository->id($vendingMachineId);
        $this->ensureVendingMachineExists($vendingMachine);

        return $vendingMachine;
    }

    /** @throws VendingMachineDoesNotExistException */
    private function ensureVendingMachineExists(?VendingMachine $vendingMachine): void
    {
        if ($vendingMachine === null) {
            throw new VendingMachineDoesNotExistException();
        }
    }
}
