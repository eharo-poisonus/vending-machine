<?php

namespace App\VendingMachine\PaymentSessions\Application\RetrievePaymentSessionPurchase;

use App\Shared\Domain\Bus\Query\Response;
use App\VendingMachine\PaymentSessions\Application\RetrievePaymentSession\PaymentSessionCurrencyResponse;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionCurrency;
use Doctrine\Common\Collections\Collection;

readonly class PurchaseResponse implements Response
{
    public function __construct(
        private string $product,
        private array $changeCurrencies
    ) {
    }

    public static function fromProductAndChangeCurrencies(string $productName, Collection $paymentSessionCurrencies): self
    {
        $changeCurrencies = $paymentSessionCurrencies->map(
            fn (PaymentSessionCurrency $paymentSessionCurrency) =>
                PaymentSessionCurrencyResponse::fromPaymentSessionCurrencies($paymentSessionCurrency)
        )->toArray();

        return new self(
            $productName,
            $changeCurrencies
        );
    }

    public function product(): string
    {
        return $this->product;
    }

    public function changeCurrencies(): array
    {
        return $this->changeCurrencies;
    }
}
