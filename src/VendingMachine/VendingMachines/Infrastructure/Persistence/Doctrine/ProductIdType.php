<?php

namespace App\VendingMachine\VendingMachines\Infrastructure\Persistence\Doctrine;

use App\Shared\Infrastructure\Persistence\Doctrine\Type\UuidType;
use App\VendingMachine\VendingMachines\Domain\ProductId;

class ProductIdType extends UuidType
{
    protected function typeClassName(): string
    {
        return ProductId::class;
    }
}
