<?php

namespace App\VendingMachine\VendingMachines\Domain;

use App\Shared\Domain\ValueObject\Money;
use App\VendingMachine\VendingMachines\Domain\Exception\ProductOutOfStockException;
use App\VendingMachine\VendingMachines\Domain\Exception\ProductStockAddedCanNotBeNegativeException;

class Product
{
    public function __construct(
        private ProductId $id,
        private VendingMachine $vendingMachine,
        private string $name,
        private string $code,
        private Money $price,
        private int $stock
    ) {
    }

    public function id(): ProductId
    {
        return $this->id;
    }

    public function setId(ProductId $id): void
    {
        $this->id = $id;
    }

    public function vendingMachine(): VendingMachine
    {
        return $this->vendingMachine;
    }

    public function setVendingMachine(VendingMachine $vendingMachine): void
    {
        $this->vendingMachine = $vendingMachine;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function price(): Money
    {
        return $this->price;
    }

    public function setPriceInCents(Money $price): void
    {
        $this->price = $price;
    }

    public function stock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): void
    {
        $this->stock = $stock;
    }

    /** @throws ProductStockAddedCanNotBeNegativeException */
    public function addStock(int $amount): void
    {
        if ($amount < 0) {
            throw new ProductStockAddedCanNotBeNegativeException();
        }

        $this->stock += $amount;
    }

    public function hasStock(): bool
    {
        return $this->stock > 0;
    }

    /** @throws ProductOutOfStockException */
    public function decreaseStock(): void
    {
        if (!$this->hasStock()) {
            throw new ProductOutOfStockException();
        }

        $this->stock--;
    }
}
