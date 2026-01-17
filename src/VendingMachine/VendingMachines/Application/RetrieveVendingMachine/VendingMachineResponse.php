<?php

namespace App\VendingMachine\VendingMachines\Application\RetrieveVendingMachine;

use App\Shared\Domain\Bus\Query\Response;
use App\VendingMachine\VendingMachines\Domain\MachineChangeCurrency;
use App\VendingMachine\VendingMachines\Domain\Product;
use App\VendingMachine\VendingMachines\Domain\VendingMachine;

readonly class VendingMachineResponse implements Response
{
    public function __construct(
        private string $id,
        private array $products,
        private array $storedMoney,
        private string $installedAt
    ) {
    }

    public static function fromVendingMachine(VendingMachine $vendingMachine): self
    {
        return new self(
            $vendingMachine->id()->value(),
            array_map(
                fn(Product $product) => ProductResponse::fromProduct($product),
                $vendingMachine->products()->toArray()
            ),
            array_map(
                fn(MachineChangeCurrency $currency) => MachineChangeResponse::fromMachineChangeCurrency($currency),
                $vendingMachine->moneyInventory()->toArray()
            ),
            $vendingMachine->installedAt()->format('Y-m-d H:i:s')
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function products(): array
    {
        return $this->products;
    }

    public function storedMoney(): array
    {
        return $this->storedMoney;
    }

    public function installedAt(): string
    {
        return $this->installedAt;
    }
}
