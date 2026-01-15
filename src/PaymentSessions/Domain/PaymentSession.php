<?php

namespace App\PaymentSessions\Domain;

use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\ValueObject\Money;
use Doctrine\Common\Collections\Collection;

class PaymentSession extends AggregateRoot
{
    public function __construct(
        private int $id,
        private int $vendingMachineId,
        private Collection $insertedCurrencies
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function vendingMachineId(): int
    {
        return $this->vendingMachineId;
    }

    public function setVendingMachineId(int $vendingMachineId): void
    {
        $this->vendingMachineId = $vendingMachineId;
    }

    public function insertedCurrencies(): Collection
    {
        return $this->insertedCurrencies;
    }

    public function addMoney(Money $money): void
    {
        foreach ($this->insertedCurrencies as $currency) {
            if ($currency->money()->cents() === $money->cents()) {
                $currency->addCurrency();
                return;
            }
        }

        $this->insertedCurrencies->add(PaymentSessionCurrency::create($money));
    }

    public function setInsertedCurrencies(Collection $insertedCurrencies): void
    {
        $this->insertedCurrencies = $insertedCurrencies;
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
