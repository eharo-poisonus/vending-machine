<?php

namespace App\Tests\unit\VendingMachine\VendingMachines\Domain;

use App\Shared\Domain\ValueObject\Money;
use App\VendingMachine\VendingMachines\Domain\Exception\ProductStockAddedCanNotBeNegativeException;
use App\VendingMachine\VendingMachines\Domain\Product;
use App\VendingMachine\VendingMachines\Domain\ProductId;
use App\VendingMachine\VendingMachines\Domain\VendingMachine;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ProductTest extends KernelTestCase
{
    #[Test]
    public function itShouldChangeTheProductStock(): void
    {
        $product = $this->createProduct();
        $addedStock = 2;
        $expectedStock = $product->stock() + $addedStock;

        $product->addStock($addedStock);

        $this->assertEquals($expectedStock, $product->stock());
    }

    #[Test]
    public function itShouldThrowExceptionIfProductStockIsNegative(): void
    {
        $this->expectException(ProductStockAddedCanNotBeNegativeException::class);

        $product = $this->createProduct();

        $product->addStock(-2);
    }

    private function createProduct(): Product
    {
        return new Product(
            ProductId::random(),
            new VendingMachine(
                VendingMachineId::random(),
                new ArrayCollection(),
                new ArrayCollection(),
                new DateTimeImmutable()
            ),
            'Test Product',
            'GET-TEST',
            Money::fromCents(150),
            1
        );
    }
}
