<?php

namespace App\VendingMachine\VendingMachines\Application\RetrieveVendingMachine;

use App\Shared\Domain\Bus\Query\QueryHandler;
use App\Shared\Domain\Exception\InvalidUuidException;
use App\VendingMachine\VendingMachines\Domain\Exception\VendingMachineDoesNotExistException;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;

final readonly class RetrieveVendingMachineQueryHandler implements QueryHandler
{
    public function __construct(
        private VendingMachineRetrieverService $service
    ) {
    }

    /** @throws VendingMachineDoesNotExistException|InvalidUuidException */
    public function __invoke(RetrieveVendingMachineQuery $query): VendingMachineResponse
    {
        $vendingMachine = ($this->service)(
            VendingMachineId::fromString($query->vendingMachineId())
        );

        return VendingMachineResponse::fromVendingMachine($vendingMachine);
    }
}
