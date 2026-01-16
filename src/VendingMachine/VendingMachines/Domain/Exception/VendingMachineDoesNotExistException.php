<?php

namespace App\VendingMachine\VendingMachines\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;
use Exception;

class VendingMachineDoesNotExistException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            'The vending machine does not exist.'
        );
    }
}
