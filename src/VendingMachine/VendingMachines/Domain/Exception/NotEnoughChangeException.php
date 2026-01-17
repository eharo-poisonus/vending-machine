<?php

namespace App\VendingMachine\VendingMachines\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

class NotEnoughChangeException extends DomainException
{
    public function __construct()
    {
        parent::__construct('The vending machine does not have enough change.');
    }
}
