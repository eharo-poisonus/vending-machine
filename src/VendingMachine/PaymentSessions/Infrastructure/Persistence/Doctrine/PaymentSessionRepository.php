<?php

namespace App\VendingMachine\PaymentSessions\Infrastructure\Persistence\Doctrine;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineCriteriaConverter;
use App\Shared\Infrastructure\Persistence\Doctrine\DoctrineRepository;
use App\VendingMachine\PaymentSessions\Domain\PaymentSession;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionId;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionRepository as PaymentSessionRepositoryInterface;

class PaymentSessionRepository extends DoctrineRepository implements PaymentSessionRepositoryInterface
{

    public function id(PaymentSessionId $id): ?PaymentSession
    {
        return $this->repository(PaymentSession::class)->find($id);
    }

    public function search(Criteria $criteria): array
    {
        $criteriaConverted = DoctrineCriteriaConverter::convert($criteria);
        return $this->repository(PaymentSession::class)->matching($criteriaConverted)->toArray();
    }

    public function save(PaymentSession $paymentSession): void
    {
        $this->persist($paymentSession);
    }

    public function update(PaymentSession $paymentSession): void
    {
        $this->persist($paymentSession);
    }

    public function delete(PaymentSession $paymentSession): void
    {
        $this->remove($paymentSession);
    }
}
