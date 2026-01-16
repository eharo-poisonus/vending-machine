<?php

namespace App\VendingMachine\PaymentSessions\Application\CancelPaymentSession;

use App\Shared\Domain\Bus\Command\CommandHandler;
use App\Shared\Domain\Exception\InvalidUuidException;
use App\VendingMachine\PaymentSessions\Domain\Exception\PaymentSessionDoesNotExistException;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionId;
use App\VendingMachine\VendingMachines\Domain\Exception\VendingMachineDoesNotExistException;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;

final readonly class CancelPaymentSessionCommandHandler implements CommandHandler
{
    public function __construct(
        private PaymentSessionCancelerService $service
    ) {
    }

    /** @throws VendingMachineDoesNotExistException|PaymentSessionDoesNotExistException|InvalidUuidException */
    public function __invoke(CancelPaymentSessionCommand $command): void
    {
        ($this->service)(
            VendingMachineId::fromString($command->vendingMachineId()),
            PaymentSessionId::fromString($command->paymentSessionId())
        );
    }
}
