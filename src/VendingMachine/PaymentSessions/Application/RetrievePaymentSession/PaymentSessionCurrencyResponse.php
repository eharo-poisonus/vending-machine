<?php

namespace App\VendingMachine\PaymentSessions\Application\RetrievePaymentSession;

use App\VendingMachine\PaymentSessions\Domain\PaymentSessionCurrency;

readonly class PaymentSessionCurrencyResponse
{
    private function __construct(
        private float $value,
        private int $amount
    ) {
    }

    public static function fromPaymentSessionCurrencies(PaymentSessionCurrency $paymentSessionCurrency): self
    {
        return new self(
            $paymentSessionCurrency->denomination()->money()->value(),
            $paymentSessionCurrency->amount()
        );
    }

    public function value(): float
    {
        return $this->value;
    }

    public function amount(): int
    {
        return $this->amount;
    }
}
