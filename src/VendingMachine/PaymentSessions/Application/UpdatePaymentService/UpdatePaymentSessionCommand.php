<?php

namespace App\VendingMachine\PaymentSessions\Application\UpdatePaymentService;

use App\Shared\Domain\Bus\Command\Command;

final readonly class UpdatePaymentSessionCommand implements Command
{
    public function __construct(
        private string $paymentSessionId,
        private float $insertedMoney
    ) {
    }

    public function paymentSessionId(): string
    {
        return $this->paymentSessionId;
    }

    public function insertedMoney(): float
    {
        return $this->insertedMoney;
    }
}
