<?php

namespace App\VendingMachine\PaymentSessions\Domain\Exception;

use Exception;

class PaymentSessionDoesNotExistException extends Exception
{
    public function __construct()
    {
        parent::__construct(
            'The payment session does not exist.'
        );
    }
}
