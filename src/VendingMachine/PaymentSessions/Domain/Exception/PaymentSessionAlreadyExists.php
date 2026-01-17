<?php

namespace App\VendingMachine\PaymentSessions\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

class PaymentSessionAlreadyExists extends DomainException
{
    public function __construct()
    {
        parent::__construct('The vending machine is in use. Wait until the previous user finishes.');
    }
}
