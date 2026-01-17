<?php

namespace App\VendingMachine\PaymentSessions\Application\RetrievePaymentSession;

use App\Shared\Domain\Bus\Query\Response;
use App\VendingMachine\PaymentSessions\Domain\PaymentSession;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionCurrency;

readonly class PaymentSessionResponse implements Response
{
    private function __construct(
        private string $paymentSessionId,
        private string $vendingMachineId,
        private array $insertedCurrencies,
        private float $totalInsertedMoney
    ) {
    }

    public static function fromPaymentSession(PaymentSession $paymentSession): self
    {
        $insertedCurrencies = $paymentSession->insertedCurrencies()
            ->map(fn(PaymentSessionCurrency $currency) =>
                PaymentSessionCurrencyResponse::fromPaymentSessionCurrencies($currency)
            )->toArray();

        return new self(
            $paymentSession->id()->value(),
            $paymentSession->vendingMachineId()->value(),
            $insertedCurrencies,
            $paymentSession->total()->value()
        );
    }

    public function paymentSessionId(): string
    {
        return $this->paymentSessionId;
    }

    public function vendingMachineId(): string
    {
        return $this->vendingMachineId;
    }

    public function insertedCurrencies(): array
    {
        return $this->insertedCurrencies;
    }

    public function totalInsertedMoney(): float
    {
        return $this->totalInsertedMoney;
    }
}
