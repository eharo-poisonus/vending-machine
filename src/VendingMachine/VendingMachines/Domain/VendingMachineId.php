<?php

namespace App\VendingMachine\VendingMachines\Domain;

use App\Shared\Domain\ValueObject\SimpleUuid;

readonly class VendingMachineId extends SimpleUuid
{
    public static function fromString(string $value): self
    {
        return new self($value);
    }
}
