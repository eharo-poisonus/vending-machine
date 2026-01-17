<?php

namespace App\VendingMachine\VendingMachines\Domain;

class Product
{
    public function __construct(
        private ProductId $id,
        private VendingMachine $vendingMachine,
        private string $name,
        private string $code,
        private int $priceInCents,
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

    public function priceInCents(): int
    {
        return $this->priceInCents;
    }

    public function setPriceInCents(int $priceInCents): void
    {
        $this->priceInCents = $priceInCents;
    }

    public function stock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): void
    {
        $this->stock = $stock;
    }
}
