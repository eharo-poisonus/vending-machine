<?php

namespace App\Shared\Domain\Criteria\Filter;

readonly class FilterField
{
    public function __construct(
        private string $value
    ) {
    }

    public function value(): string
    {
        return $this->value;
    }
}
