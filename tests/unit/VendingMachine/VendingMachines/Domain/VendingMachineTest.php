<?php

namespace App\Tests\unit\VendingMachine\VendingMachines\Domain;

use App\Shared\Domain\ValueObject\Money;
use App\VendingMachine\Shared\Domain\CurrencyDenomination;
use App\VendingMachine\VendingMachines\Domain\Exception\CurrencyAmountAddedCanNotBeNegativeException;
use App\VendingMachine\VendingMachines\Domain\Exception\ProductStockAddedCanNotBeNegativeException;
use App\VendingMachine\VendingMachines\Domain\MachineChangeCurrency;
use App\VendingMachine\VendingMachines\Domain\Product;
use App\VendingMachine\VendingMachines\Domain\ProductId;
use App\VendingMachine\VendingMachines\Domain\VendingMachine;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class VendingMachineTest extends KernelTestCase
{
    #[Test]
    public function itShouldChangeTheCurrencyAmount(): void
    {
        $addedCurrencies = [
            1 => 10,
            2 => 2
        ];

        $vendingMachine = $this->createVendingMachine();

        $vendingMachine->addChangeCurrenciesToMoneyInventory($addedCurrencies);

        $currency = $vendingMachine->moneyInventory()
            ->filter(fn(MachineChangeCurrency $currency) => $currency->id() === 1)
            ->first();

        $this->assertSame(11, $currency->amount());

        $currency = $vendingMachine->moneyInventory()
            ->filter(fn(MachineChangeCurrency $currency) => $currency->id() === 2)
            ->first();

        $this->assertSame(12, $currency->amount());
    }

    #[Test]
    public function itShouldChangeTheProductStock(): void
    {
        $addedProducts = [
            '942e422a-d3fe-4cbf-a059-5cebd918082e' => 10,
            '47de83e6-0417-4c45-a4db-0eba81534e38' => 2
        ];

        $vendingMachine = $this->createVendingMachine();

        $vendingMachine->addStockToProducts($addedProducts);

        $product = $vendingMachine->products()
            ->filter(fn(Product $product) => $product->id()->value() === '942e422a-d3fe-4cbf-a059-5cebd918082e')
            ->first();

        $this->assertSame(10, $product->stock());

        $product = $vendingMachine->products()
            ->filter(fn(Product $product) => $product->id()->value() === '47de83e6-0417-4c45-a4db-0eba81534e38')
            ->first();

        $this->assertSame(12, $product->stock());
    }

    #[Test]
    public function itShouldThrowExceptionIfCurrencyAmountIsNegative(): void
    {
        $addedCurrencies = [
            1 => 10,
            2 => -2
        ];

        $this->expectException(CurrencyAmountAddedCanNotBeNegativeException::class);

        $vendingMachine = $this->createVendingMachine();

        $vendingMachine->addChangeCurrenciesToMoneyInventory($addedCurrencies);
    }

    #[Test]
    public function itShouldThrowExceptionIfProductStockIsNegative(): void
    {
        $addedProducts = [
            '942e422a-d3fe-4cbf-a059-5cebd918082e' => 10,
            '47de83e6-0417-4c45-a4db-0eba81534e38' => -2
        ];

        $this->expectException(ProductStockAddedCanNotBeNegativeException::class);

        $vendingMachine = $this->createVendingMachine();

        $vendingMachine->addStockToProducts($addedProducts);
    }

    private function createVendingMachine(): VendingMachine
    {
        $vendingMachine =  new VendingMachine(
            VendingMachineId::random(),
            new ArrayCollection(),
            new ArrayCollection(),
            new DateTimeImmutable()
        );

        $vendingMachine->setProducts(new ArrayCollection([
            new Product(
                ProductId::fromString('942e422a-d3fe-4cbf-a059-5cebd918082e'),
                $vendingMachine,
                'Test Product',
                'GET-TEST',
                Money::fromCents(200),
                0
            ),
            new Product(
                ProductId::fromString('47de83e6-0417-4c45-a4db-0eba81534e38'),
                $vendingMachine,
                'Test Product 2',
                'GET-TEST-2',
                Money::fromCents(100),
                10
            )
        ]));

        $vendingMachine->setMoneyInventory(
            new ArrayCollection([
                new MachineChangeCurrency(
                    1,
                    $vendingMachine,
                    new CurrencyDenomination(
                        1,
                        Money::fromCents(5)
                    ),
                    1
                ),
                new MachineChangeCurrency(
                    2,
                    $vendingMachine,
                    new CurrencyDenomination(
                        2,
                        Money::fromCents(10)
                    ),
                    10
                )
            ]),
        );

        return $vendingMachine;
    }
}
