<?php

namespace App\VendingMachine\PaymentSessions\Application\RetrievePaymentSession;

use App\Shared\Domain\Bus\Query\Query;

readonly class RetrievePaymentSessionQuery implements Query
{
    public function __construct(
        private string $paymentSessionId
    ) {
    }

    public function paymentSessionId(): string
    {
        return $this->paymentSessionId;
    }
}
