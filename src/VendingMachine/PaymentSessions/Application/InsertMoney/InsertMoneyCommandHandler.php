<?php

namespace App\VendingMachine\PaymentSessions\Application\InsertMoney;

use App\Shared\Domain\Bus\Command\CommandHandler;
use App\Shared\Domain\Exception\InvalidUuidException;
use App\Shared\Domain\Exception\NotAllowedCurrencyDenominationException;
use App\Shared\Domain\ValueObject\Money;
use App\VendingMachine\PaymentSessions\Domain\Exception\InvalidDenominationException;
use App\VendingMachine\PaymentSessions\Domain\Exception\MultiplePaymentSessionsActiveException;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionId;
use App\VendingMachine\VendingMachines\Domain\Exception\VendingMachineDoesNotExistException;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;

final readonly class InsertMoneyCommandHandler implements CommandHandler
{
    public function __construct(
        private MoneyInserterService $service
    ) {
    }

    /**
     * @throws MultiplePaymentSessionsActiveException|NotAllowedCurrencyDenominationException
     * @throws InvalidDenominationException|VendingMachineDoesNotExistException|InvalidUuidException
     */
    public function __invoke(InsertMoneyCommand $command): void
    {
        ($this->service)(
            VendingMachineId::fromString($command->vendingMachineId()),
            PaymentSessionId::fromString($command->paymentSessionId()),
            Money::fromFloat($command->insertedMoney())
        );
    }
}
