<?php

namespace App\VendingMachine\PaymentSessions\Application\CompletePaymentSession;

use App\Shared\Domain\Bus\Command\Command;

readonly class CompletePaymentSessionCommand implements Command
{
    public function __construct(
        private string $vendingMachineId,
        private string $paymentSessionId,
        private string $productCode
    ) {
    }

    public function vendingMachineId(): string
    {
        return $this->vendingMachineId;
    }

    public function paymentSessionId(): string
    {
        return $this->paymentSessionId;
    }

    public function productCode(): string
    {
        return $this->productCode;
    }
}
