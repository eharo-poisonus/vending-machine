<?php

namespace App\VendingMachine\PaymentSessions\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

class NotEnoughMoneyException extends DomainException
{
    public function __construct()
    {
        parent::__construct('Not enough money.');
    }
}
