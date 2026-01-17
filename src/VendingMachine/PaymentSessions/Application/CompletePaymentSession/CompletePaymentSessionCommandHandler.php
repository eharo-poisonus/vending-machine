<?php

namespace App\VendingMachine\PaymentSessions\Application\CompletePaymentSession;

use App\Shared\Domain\Bus\Command\CommandHandler;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionId;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;

final readonly class CompletePaymentSessionCommandHandler implements CommandHandler
{
    public function __construct(
        private PaymentSessionCompleterService $service
    ) {
    }

    public function __invoke(CompletePaymentSessionCommand $command): void
    {
        ($this->service)(
            VendingMachineId::fromString($command->vendingMachineId()),
            PaymentSessionId::fromString($command->paymentSessionId()),
            $command->productCode()
        );
    }
}
