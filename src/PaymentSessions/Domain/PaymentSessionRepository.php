<?php

namespace App\PaymentSessions\Domain;

interface PaymentSessionRepository
{
    public function id(int $id): ?PaymentSession;
    public function save(PaymentSession $paymentSession): void;
    public function update(PaymentSession $paymentSession): void;
    public function remove(PaymentSession $paymentSession): void;
}
