<?php

namespace App\Shared\Domain\Criteria\Filter;

readonly class FilterFieldsMap
{
    public function __construct(
        private array $fieldsMap = []
    ) {
    }

    public function mapValueToField($value): string
    {
        return $this->fieldsMap[$value] ?? $value;
    }
}
