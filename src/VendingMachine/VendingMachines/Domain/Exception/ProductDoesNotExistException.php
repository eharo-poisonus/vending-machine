<?php

namespace App\VendingMachine\VendingMachines\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

class ProductDoesNotExistException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Product does not exist.');
    }
}
