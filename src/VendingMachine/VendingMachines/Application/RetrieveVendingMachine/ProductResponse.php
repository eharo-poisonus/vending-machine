<?php

namespace App\VendingMachine\VendingMachines\Application\RetrieveVendingMachine;

use App\Shared\Domain\Bus\Query\Response;
use App\VendingMachine\VendingMachines\Domain\Product;

readonly class ProductResponse implements Response
{
    public function __construct(
        private string $id,
        private string $code,
        private int $price,
        private int $stock
    ) {
    }

    public static function fromProduct(Product $product): self
    {
        $productPrice = $product->priceInCents() / 100;

        return new self(
            $product->id()->value(),
            $product->code(),
            $productPrice,
            $product->stock()
        );
    }

    public function id(): string
    {
        return $this->id;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function price(): int
    {
        return $this->price;
    }

    public function stock(): int
    {
        return $this->stock;
    }
}
