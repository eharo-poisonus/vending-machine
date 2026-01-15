<?php

namespace App\Shared\Domain\ValueObject;

use App\Shared\Domain\Exception\InvalidUuidException;

readonly class SimpleUuid extends Uuid
{
    /** @throws InvalidUuidException */
    public static function fromString(string $value): SimpleUuid
    {
        return new static($value);
    }
}
