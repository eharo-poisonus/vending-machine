<?php

namespace App\VendingMachines\Domain;

interface VendingMachineRepository
{
    public function id(int $id): ?VendingMachine;
    public function update(VendingMachine $paymentSession): void;
}
