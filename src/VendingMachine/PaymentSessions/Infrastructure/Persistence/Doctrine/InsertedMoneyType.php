<?php

namespace App\VendingMachine\PaymentSessions\Infrastructure\Persistence\Doctrine;

use App\Shared\Domain\ValueObject\Money;
use App\VendingMachine\Shared\Infrastructure\Persistence\Doctrine\MoneyType;

class InsertedMoneyType extends MoneyType
{
    protected function typeClassName(): string
    {
        return Money::class;
    }
}
