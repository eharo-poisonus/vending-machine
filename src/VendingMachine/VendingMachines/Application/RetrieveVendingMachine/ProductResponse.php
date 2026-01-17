<?php

namespace App\VendingMachine\VendingMachines\Application\RetrieveVendingMachine;

use App\Shared\Domain\Bus\Query\Response;
use App\VendingMachine\VendingMachines\Domain\Product;

readonly class ProductResponse implements Response
{
    public function __construct(
        private string $id,
        private string $name,
        private string $code,
        private float $price,
        private int $stock
    ) {
    }

    public static function fromProduct(Product $product): self
    {
        return new self(
            $product->id()->value(),
            $product->name(),
            $product->code(),
            $product->price()->value(),
            $product->stock()
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function price(): float
    {
        return $this->price;
    }

    public function stock(): int
    {
        return $this->stock;
    }
}
