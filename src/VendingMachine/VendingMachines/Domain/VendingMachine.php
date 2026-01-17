<?php

namespace App\VendingMachine\VendingMachines\Domain;

use App\Shared\Domain\Aggregate\AggregateRoot;
use App\Shared\Domain\Exception\NotAllowedCurrencyDenominationException;
use App\Shared\Domain\ValueObject\Money;
use App\VendingMachine\PaymentSessions\Domain\Exception\NotEnoughMoneyException;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionCurrency;
use App\VendingMachine\VendingMachines\Domain\Exception\CurrencyAmountAddedCanNotBeNegativeException;
use App\VendingMachine\VendingMachines\Domain\Exception\NotEnoughChangeException;
use App\VendingMachine\VendingMachines\Domain\Exception\ProductDoesNotExistException;
use App\VendingMachine\VendingMachines\Domain\Exception\ProductOutOfStockException;
use App\VendingMachine\VendingMachines\Domain\Exception\ProductStockAddedCanNotBeNegativeException;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;

class VendingMachine extends AggregateRoot
{
    public function __construct(
        private VendingMachineId $id,
        private Collection $products,
        private Collection $moneyInventory,
        private DateTimeImmutable $installedAt
    ) {
    }

    public function id(): VendingMachineId
    {
        return $this->id;
    }

    public function setId(VendingMachineId $id): void
    {
        $this->id = $id;
    }

    public function installedAt(): DateTimeImmutable
    {
        return $this->installedAt;
    }

    public function setInstalledAt(DateTimeImmutable $installedAt): void
    {
        $this->installedAt = $installedAt;
    }

    public function products(): Collection
    {
        return $this->products;
    }

    public function setProducts(Collection $products): void
    {
        $this->products = $products;
    }

    public function moneyInventory(): Collection
    {
        return $this->moneyInventory;
    }

    public function setMoneyInventory(Collection $moneyInventory): void
    {
        $this->moneyInventory = $moneyInventory;
    }

    /** @throws ProductDoesNotExistException */
    public function productByCode(string $productCode): Product
    {
        foreach ($this->products as $product) {
            if ($product->code() === $productCode) {
                return $product;
            }
        }

        throw new ProductDoesNotExistException();
    }

    /** @throws ProductStockAddedCanNotBeNegativeException */
    public function addStockToProducts(array $addedStocks): void
    {
        /** @var Product $product */
        foreach ($this->products as $product) {
            $productId = $product->id()->value();

            if (isset($addedStocks[$productId])) {
                $product->addStock($addedStocks[$productId]);
            }
        }
    }

    /** @throws CurrencyAmountAddedCanNotBeNegativeException */
    public function addChangeCurrenciesToMoneyInventory(array $addedMoney): void
    {
        /** @var MachineChangeCurrency $machineChangeCurrency */
        foreach ($this->moneyInventory as $machineChangeCurrency) {
            $machineChangeCurrencyId = $machineChangeCurrency->id();

            if (isset($addedMoney[$machineChangeCurrencyId])) {
                $machineChangeCurrency->addAmount($addedMoney[$machineChangeCurrencyId]);
            }
        }
    }

    /** @throws NotAllowedCurrencyDenominationException */
    public function collectMoney(Collection $insertedCurrencies): void
    {
        /** @var PaymentSessionCurrency $insertedCurrency */
        foreach ($insertedCurrencies as $insertedCurrency) {
            $existingCurrency = $this->moneyInventory
                ->filter(
                    fn(MachineChangeCurrency $machineChangeCurrency) => $machineChangeCurrency->denomination()->id(
                        ) === $insertedCurrency->denomination()->id()
                )->first();

            if (!$existingCurrency) {
                throw new NotAllowedCurrencyDenominationException();
            }

            $existingCurrency->addAmount($insertedCurrency->amount());
        }
    }

    /**  @throws ProductOutOfStockException */
    public function dispenseProduct(Product $product): void
    {
        $product->decreaseStock();
    }

    /** @throws NotAllowedCurrencyDenominationException|NotEnoughChangeException */
    public function dispenseChange(array $changeToDispense): void
    {
        foreach ($changeToDispense as $currency) {
            /** @var MachineChangeCurrency $existing */
            $existing = $this->moneyInventory
                ->filter(fn(MachineChangeCurrency $c) => $c->denomination()->id() === $currency->denomination()->id())
                ->first();

            if (!$existing) {
                throw new NotAllowedCurrencyDenominationException();
            }

            if ($existing->amount() < $currency->amount()) {
                throw new NotEnoughChangeException();
            }

            $existing->removeAmount($currency->amount());
        }
    }

    /** @throws NotEnoughMoneyException */
    public function calculateChange(Money $clientInsertedTotalMoney, Money $productPrice): array
    {
        $changeAmount = $clientInsertedTotalMoney->cents() - $productPrice->cents();

        if ($changeAmount < 0) {
            return [];
        }

        $changeCurrencies = [];

        /** @var MachineChangeCurrency $changeCurrency */
        foreach ($this->orderMoneyInventoryDescendingByDenomination() as $changeCurrency) {
            $changeCurrencyValue = $changeCurrency->denomination()->money()->cents();
            $changeCurrencyAvailable = $changeCurrency->amount();

            if ($changeCurrencyAvailable <= 0 || $changeCurrencyValue > $changeAmount) {
                continue;
            }

            $numberOfCurrencies = min((int)floor($changeAmount / $changeCurrencyValue), $changeCurrencyAvailable);

            if ($numberOfCurrencies > 0) {
                $changeCurrencies[] = new MachineChangeCurrency(
                    $changeCurrency->id(),
                    $this,
                    $changeCurrency->denomination(),
                    $numberOfCurrencies
                );

                $changeAmount -= $changeCurrencyValue * $numberOfCurrencies;
            }

            if ($changeAmount <= 0) {
                break;
            }
        }

        if ($changeAmount > 0) {
            throw new NotEnoughMoneyException();
        }

        return $changeCurrencies;
    }

    private function orderMoneyInventoryDescendingByDenomination(): array
    {
        $sortedInventory = $this->moneyInventory()->toArray();
        usort(
            $sortedInventory,
            fn(MachineChangeCurrency $a, MachineChangeCurrency $b) => $b->denomination()->money()->cents(
                ) <=> $a->denomination()->money()->cents()
        );

        return $sortedInventory;
    }
}
