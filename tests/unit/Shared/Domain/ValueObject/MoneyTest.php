<?php

namespace App\Tests\unit\Shared\Domain\ValueObject;

use App\PaymentSessions\Domain\Exception\InvalidDenominationException;
use App\Shared\Domain\ValueObject\Money;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class MoneyTest extends TestCase
{
    #[Test]
    #[DataProvider('moneyValuesProvider')]
    public function itShouldCreateMoneyFromFloat(float $input, int $expected): void
    {
        $money = Money::fromFloat($input);
        $this->assertSame($expected, $money->cents());
    }

    #[Test]
    #[DataProvider('moneyValuesProvider')]
    public function itShouldCreateMoneyFromCents(float $expected, int $input): void
    {
        $money = Money::fromCents($input);
        $this->assertSame($expected, $money->value());
    }

    #[Test]
    public function itShouldThrowExceptionOnInvalidDenominationFromCents(): void
    {
        $this->expectException(InvalidDenominationException::class);
        Money::fromCents(1);
    }

    #[Test]
    public function itShouldThrowExceptionOnInvalidDenominationFromFloat(): void
    {
        $this->expectException(InvalidDenominationException::class);
        Money::fromFloat(0.20);
    }

    public static function moneyValuesProvider(): array
    {
        return [
            [0.05, 5],
            [0.10, 10],
            [0.25, 25],
            [1.00, 100]
        ];
    }
}
