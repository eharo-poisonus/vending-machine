<?php

namespace App\VendingMachine\Shared\Domain;

use App\Shared\Domain\Criteria\Criteria;

interface CurrencyDenominationRepository
{
    public function search(Criteria $criteria): array;
}
