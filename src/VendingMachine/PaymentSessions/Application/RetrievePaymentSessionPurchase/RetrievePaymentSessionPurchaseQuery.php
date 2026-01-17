<?php

namespace App\VendingMachine\PaymentSessions\Application\RetrievePaymentSessionPurchase;

use App\Shared\Domain\Bus\Query\Query;

readonly class RetrievePaymentSessionPurchaseQuery implements Query
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
