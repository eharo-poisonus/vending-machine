<?php

namespace App\VendingMachine\PaymentSessions\Domain;

use App\Shared\Domain\ValueObject\SimpleUuid;

readonly class PaymentSessionId extends SimpleUuid
{
    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
