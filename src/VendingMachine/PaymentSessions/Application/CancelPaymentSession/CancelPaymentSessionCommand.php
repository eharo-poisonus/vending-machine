<?php

namespace App\VendingMachine\PaymentSessions\Application\CancelPaymentSession;

use App\Shared\Domain\Bus\Command\Command;

readonly class CancelPaymentSessionCommand implements Command
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
