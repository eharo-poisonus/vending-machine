<?php

namespace App\VendingMachine\VendingMachines\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

class ProductOutOfStockException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Product out of stock.');
    }
}
