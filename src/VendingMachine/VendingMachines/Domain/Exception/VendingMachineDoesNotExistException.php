<?php

namespace App\VendingMachine\VendingMachines\Domain\Exception;

use Exception;

class VendingMachineDoesNotExistException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            'The vending machine does not exist.'
        );
    }
}
