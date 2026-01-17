<?php

namespace App\VendingMachine\PaymentSessions\Application\CancelPaymentSession;

use App\Shared\Domain\Bus\Command\CommandHandler;
use App\Shared\Domain\Exception\InvalidUuidException;
use App\VendingMachine\PaymentSessions\Domain\Exception\PaymentSessionDoesNotExistException;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionId;

final readonly class CancelPaymentSessionCommandHandler implements CommandHandler
{
    public function __construct(
        private PaymentSessionCancelerService $service
    ) {
    }

    /** @throws PaymentSessionDoesNotExistException|InvalidUuidException */
    public function __invoke(CancelPaymentSessionCommand $command): void
    {
        ($this->service)(
            PaymentSessionId::fromString($command->paymentSessionId())
        );
    }
}
