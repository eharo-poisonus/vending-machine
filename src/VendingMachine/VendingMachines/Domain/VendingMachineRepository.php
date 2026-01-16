<?php

namespace App\VendingMachine\VendingMachines\Domain;

interface VendingMachineRepository
{
    public function id(VendingMachineId $id): ?VendingMachine;
    public function update(VendingMachine $paymentSession): void;
}
