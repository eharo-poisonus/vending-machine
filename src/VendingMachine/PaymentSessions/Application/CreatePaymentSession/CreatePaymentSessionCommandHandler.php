<?php

namespace App\VendingMachine\PaymentSessions\Application\CreatePaymentSession;

use App\Shared\Domain\Bus\Command\CommandHandler;
use App\Shared\Domain\Exception\InvalidUuidException;
use App\Shared\Domain\Exception\NotAllowedCurrencyDenominationException;
use App\Shared\Domain\ValueObject\Money;
use App\VendingMachine\PaymentSessions\Domain\Exception\InvalidDenominationException;
use App\VendingMachine\PaymentSessions\Domain\Exception\PaymentSessionAlreadyExists;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionId;
use App\VendingMachine\VendingMachines\Domain\Exception\VendingMachineDoesNotExistException;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;

final readonly class CreatePaymentSessionCommandHandler implements CommandHandler
{
    public function __construct(
        private PaymentSessionCreatorService $service
    ) {
    }

    /**
     * @throws InvalidDenominationException|VendingMachineDoesNotExistException
     * @throws NotAllowedCurrencyDenominationException|InvalidUuidException|PaymentSessionAlreadyExists
     */
    public function __invoke(CreatePaymentSessionCommand $command): void
    {
        ($this->service)(
            VendingMachineId::fromString($command->vendingMachineId()),
            PaymentSessionId::fromString($command->paymentSessionId()),
            Money::fromFloat($command->insertedMoney())
        );
    }
}
