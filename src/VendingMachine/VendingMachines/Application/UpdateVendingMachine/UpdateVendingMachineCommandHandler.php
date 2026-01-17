<?php

namespace App\VendingMachine\VendingMachines\Application\UpdateVendingMachine;

use App\Shared\Domain\Bus\Command\CommandHandler;
use App\Shared\Domain\Exception\InvalidUuidException;
use App\VendingMachine\VendingMachines\Domain\Exception\CurrencyAmountAddedCanNotBeNegativeException;
use App\VendingMachine\VendingMachines\Domain\Exception\ProductStockAddedCanNotBeNegativeException;
use App\VendingMachine\VendingMachines\Domain\Exception\VendingMachineDoesNotExistException;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;

final readonly class UpdateVendingMachineCommandHandler implements CommandHandler
{
    public function __construct(
        private VendingMachineUpdaterService $service
    ) {
    }

    /**
     * @throws VendingMachineDoesNotExistException|ProductStockAddedCanNotBeNegativeException
     * @throws InvalidUuidException|CurrencyAmountAddedCanNotBeNegativeException
     * */
    public function __invoke(UpdateVendingMachineCommand $command): void
    {
        ($this->service)(
            VendingMachineId::fromString($command->vendingMachineId()),
            $command->updatedProducts(),
            $command->updatedChangeMoney()
        );
    }
}
