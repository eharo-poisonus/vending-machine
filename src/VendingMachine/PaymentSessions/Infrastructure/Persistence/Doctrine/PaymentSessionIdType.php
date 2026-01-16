<?php

namespace App\VendingMachine\PaymentSessions\Infrastructure\Persistence\Doctrine;

use App\Shared\Infrastructure\Persistence\Doctrine\Type\UuidType;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionId;

class PaymentSessionIdType extends UuidType
{
    protected function typeClassName(): string
    {
        return PaymentSessionId::class;
    }
}
