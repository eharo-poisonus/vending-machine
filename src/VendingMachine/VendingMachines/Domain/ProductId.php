<?php

namespace App\VendingMachine\VendingMachines\Domain;

use App\Shared\Domain\ValueObject\SimpleUuid;

readonly class ProductId extends SimpleUuid
{
    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
