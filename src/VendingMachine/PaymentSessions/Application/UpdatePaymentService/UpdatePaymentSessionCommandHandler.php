<?php

namespace App\VendingMachine\PaymentSessions\Application\UpdatePaymentService;

use App\Shared\Domain\Bus\Command\CommandHandler;
use App\Shared\Domain\Exception\InvalidUuidException;
use App\Shared\Domain\Exception\NotAllowedCurrencyDenominationException;
use App\Shared\Domain\ValueObject\Money;
use App\VendingMachine\PaymentSessions\Domain\Exception\InvalidDenominationException;
use App\VendingMachine\PaymentSessions\Domain\Exception\PaymentSessionDoesNotExistException;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionId;

final readonly class UpdatePaymentSessionCommandHandler implements CommandHandler
{
    public function __construct(
        private PaymentSessionUpdaterService $service
    ) {
    }

    /** @throws InvalidDenominationException|PaymentSessionDoesNotExistException|NotAllowedCurrencyDenominationException|InvalidUuidException */
    public function __invoke(UpdatePaymentSessionCommand $command): void
    {
        ($this->service)(
            PaymentSessionId::fromString($command->paymentSessionId()),
            Money::fromFloat($command->insertedMoney())
        );
    }
}
