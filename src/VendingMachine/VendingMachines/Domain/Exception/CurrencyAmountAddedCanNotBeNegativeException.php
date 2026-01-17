<?php

namespace App\VendingMachine\VendingMachines\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

class CurrencyAmountAddedCanNotBeNegativeException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Machine change currency amount can not be negative.');
    }
}
