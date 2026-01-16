<?php

namespace App\VendingMachine\PaymentSessions\Domain;

use App\Shared\Domain\Aggregate\AggregateRoot;
use App\VendingMachine\Shared\Domain\CurrencyDenomination;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;
use Doctrine\Common\Collections\Collection;

class PaymentSession extends AggregateRoot
{
    public function __construct(
        private PaymentSessionId $id,
        private VendingMachineId $vendingMachineId,
        private Collection $insertedCurrencies
    ) {
    }

    public function id(): PaymentSessionId
    {
        return $this->id;
    }

    public function setId(PaymentSessionId $id): void
    {
        $this->id = $id;
    }

    public function vendingMachineId(): VendingMachineId
    {
        return $this->vendingMachineId;
    }

    public function setVendingMachineId(VendingMachineId $vendingMachineId): void
    {
        $this->vendingMachineId = $vendingMachineId;
    }

    public function insertedCurrencies(): Collection
    {
        return $this->insertedCurrencies;
    }

    public function setInsertedCurrencies(Collection $insertedCurrencies): void
    {
        $this->insertedCurrencies = $insertedCurrencies;
    }

    public function addCurrency(CurrencyDenomination $currencyDenomination): void
    {
        /** @var PaymentSessionCurrency $currency */
        foreach ($this->insertedCurrencies as $currency) {
            if ($currency->denomination()->money()->cents() === $currencyDenomination->money()->cents()) {
                $currency->addCurrency();
                return;
            }
        }

        $this->insertedCurrencies->add(
            PaymentSessionCurrency::create($this, $currencyDenomination)
        );
    }

    public function total(): int
    {
        return array_sum(
            $this->insertedCurrencies->map(
                fn(PaymentSessionCurrency $paymentSessionCurrency) =>
                    $paymentSessionCurrency->denomination()->cents() * $paymentSessionCurrency->amount()
            )->toArray()
        );
    }
}
