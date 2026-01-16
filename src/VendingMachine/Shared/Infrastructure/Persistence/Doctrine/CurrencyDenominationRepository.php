<?php

namespace App\VendingMachine\Shared\Infrastructure\Persistence\Doctrine;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineCriteriaConverter;
use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineRepository;
use App\VendingMachine\Shared\Domain\CurrencyDenomination;
use App\VendingMachine\Shared\Domain\CurrencyDenominationRepository as CurrencyDenominationRepositoryInterface;

class CurrencyDenominationRepository extends DoctrineRepository implements CurrencyDenominationRepositoryInterface
{
    public function search(Criteria $criteria): array
    {
        $criteriaConverted = DoctrineCriteriaConverter::convert($criteria);
        return $this->repository(CurrencyDenomination::class)->matching($criteriaConverted)->toArray();
    }
}
