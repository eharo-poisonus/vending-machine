<?php

namespace App\VendingMachine\VendingMachines\Infrastructure\Persistence\Doctrine;

use App\Shared\Infrastructure\Persistence\Doctrine\Type\UuidType;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;

class VendingMachineIdType extends UuidType
{
    protected function typeClassName(): string
    {
        return VendingMachineId::class;
    }
}
