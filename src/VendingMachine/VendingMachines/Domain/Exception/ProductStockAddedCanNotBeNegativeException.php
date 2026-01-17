<?php

namespace App\VendingMachine\VendingMachines\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

class ProductStockAddedCanNotBeNegativeException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Product stock can not be negative.');
    }
}
