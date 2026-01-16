<?php

namespace App\VendingMachine\PaymentSessions\Domain;

use App\Shared\Domain\Criteria\Criteria;

interface PaymentSessionRepository
{
    public function id(PaymentSessionId $id): ?PaymentSession;
    public function search(Criteria $criteria): array;
    public function save(PaymentSession $paymentSession): void;
    public function update(PaymentSession $paymentSession): void;
    public function delete(PaymentSession $paymentSession): void;
}
