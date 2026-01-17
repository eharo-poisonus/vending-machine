<?php

namespace App\Tests\unit\VendingMachine\VendingMachines\Domain;

use App\Shared\Domain\ValueObject\Money;
use App\VendingMachine\Shared\Domain\CurrencyDenomination;
use App\VendingMachine\VendingMachines\Domain\Exception\CurrencyAmountAddedCanNotBeNegativeException;
use App\VendingMachine\VendingMachines\Domain\MachineChangeCurrency;
use App\VendingMachine\VendingMachines\Domain\VendingMachine;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MachineChangeCurrencyTest extends KernelTestCase
{
    #[Test]
    public function itShouldChangeTheCurrencyAmount(): void
    {
        $machineChangeCurrency = $this->createMachineChangeCurrency();
        $addedAmount = 2;
        $expectedAmount = $machineChangeCurrency->amount() + $addedAmount;

        $machineChangeCurrency->addAmount($addedAmount);

        $this->assertEquals($expectedAmount, $machineChangeCurrency->amount());
    }

    #[Test]
    public function itShouldThrowExceptionIfCurrencyAmountIsNegative(): void
    {
        $this->expectException(CurrencyAmountAddedCanNotBeNegativeException::class);

        $machineChangeCurrency = $this->createMachineChangeCurrency();

        $machineChangeCurrency->addAmount(-2);
    }

    private function createMachineChangeCurrency(): MachineChangeCurrency
    {
        return new MachineChangeCurrency(
            1,
            new VendingMachine(
                VendingMachineId::random(),
                new ArrayCollection(),
                new ArrayCollection(),
                new DateTimeImmutable()
            ),
            new CurrencyDenomination(
                1,
                Money::fromCents(10)
            ),
            1
        );
    }
}
