<?php

namespace App\VendingMachine\PaymentSessions\Domain\Exception;

use App\Shared\Domain\Exception\DomainException;

class PaymentSessionDoesNotExistException extends DomainException
{
    public function __construct()
    {
        parent::__construct(
            'The payment session does not exist.'
        );
    }
}
