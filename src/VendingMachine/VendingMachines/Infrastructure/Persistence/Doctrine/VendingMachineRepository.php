<?php

namespace App\VendingMachine\VendingMachines\Infrastructure\Persistence\Doctrine;

use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineRepository;
use App\VendingMachine\VendingMachines\Domain\VendingMachine;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;
use App\VendingMachine\VendingMachines\Domain\VendingMachineRepository as VendingMachineRepositoryInterface;

class VendingMachineRepository extends DoctrineRepository implements VendingMachineRepositoryInterface
{

    public function id(VendingMachineId $id): ?VendingMachine
    {
        return $this->repository(VendingMachine::class)->find($id);
    }

    public function update(VendingMachine $paymentSession): void
    {
        $this->persist($paymentSession);
    }
}
