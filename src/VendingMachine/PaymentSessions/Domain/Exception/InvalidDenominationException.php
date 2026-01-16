<?php

namespace App\VendingMachine\PaymentSessions\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

class InvalidDenominationException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Invalid denomination. The accepted denominations are: 0.05, 0.10, 0.25, 1');
    }
}
