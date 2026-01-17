<?php

namespace App\VendingMachine\PaymentSessions\Domain;

use App\VendingMachine\Shared\Domain\CurrencyDenomination;

class PaymentSessionCurrency
{
    private const int FIRS_INSERTED_AMOUNT = 1;

    private int $id;

    public function __construct(
        private PaymentSession $paymentSession,
        private CurrencyDenomination $denomination,
        private int $amount
    ) {
    }

    public static function create(PaymentSession $paymentSession, CurrencyDenomination $denomination): self
    {
        return new self($paymentSession, $denomination, self::FIRS_INSERTED_AMOUNT);
    }

    public function id(): int
    {
        return $this->id;
    }

    public function paymentSession(): PaymentSession
    {
        return $this->paymentSession;
    }

    public function setPaymentSession(PaymentSession $paymentSession): void
    {
        $this->paymentSession = $paymentSession;
    }

    public function denomination(): CurrencyDenomination
    {
        return $this->denomination;
    }

    public function setDenomination(CurrencyDenomination $denomination): void
    {
        $this->denomination = $denomination;
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function setAmount(int $amount): void
    {
        $this->amount = $amount;
    }

    public function addCurrency(): void
    {
        $this->amount++;
    }
}
