<?php

namespace App\Console;

use App\Shared\Domain\Bus\Command\CommandBus;
use App\Shared\Domain\Bus\Query\QueryBus;
use App\Shared\Domain\Exception\DomainException;
use App\Shared\Domain\ValueObject\SimpleUuid;
use App\VendingMachine\PaymentSessions\Application\CreatePaymentSession\CreatePaymentSessionCommand;
use App\VendingMachine\PaymentSessions\Application\RetrievePaymentSession\PaymentSessionResponse;
use App\VendingMachine\PaymentSessions\Application\RetrievePaymentSession\RetrievePaymentSessionQuery;
use App\VendingMachine\PaymentSessions\Application\UpdatePaymentService\UpdatePaymentSessionCommand;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'vending-machine:insert-money')]
class InsertMoneyConsoleCommand extends BaseConsoleCommand
{
    public function __construct(
        private readonly CommandBus $commandBus,
        private readonly QueryBus $queryBus,
        ?string $name = null,
        ?callable $code = null
    ) {
        parent::__construct($name, $code);
    }

    public function __invoke(#[Argument('The money inserted')] float $insertedMoney, OutputInterface $output): int
    {
        try {
            $actualPaymentSessionId = $this->createOrUpdatePaymentSession($insertedMoney);

            $paymentSession = $this->retrievePaymentSession($actualPaymentSessionId);

            $output->writeln(sprintf('Current balance: %s', $paymentSession->totalInsertedMoney()));

            return Command::SUCCESS;
        } catch (DomainException $exception) {
            $output->writeln($exception->getMessage());

            return Command::FAILURE;
        }
    }

    private function createOrUpdatePaymentSession(float $insertedMoney): string
    {
        $actualPaymentSessionId = $this->retrievePaymentSessionIdFromMemory();

        if (null === $actualPaymentSessionId) {
            $newPaymentSessionId = SimpleUuid::random()->value();

            $this->createPaymentSession($newPaymentSessionId, $insertedMoney);

            return $newPaymentSessionId;
        }

        $this->updatePaymentSession($actualPaymentSessionId, $insertedMoney);

        return $actualPaymentSessionId;
    }

    private function createPaymentSession(string $paymentSessionId, float $insertedMoney): void
    {
        $this->storeCreatedPaymentSessionIdInMemory($paymentSessionId);

        $this->commandBus->dispatch(
            new CreatePaymentSessionCommand(
                self::VENDING_MACHINE_ID,
                $paymentSessionId,
                $insertedMoney
            )
        );
    }

    private function updatePaymentSession(string $paymentSessionId, float $insertedMoney): void
    {
        $this->commandBus->dispatch(
            new UpdatePaymentSessionCommand(
                $paymentSessionId,
                $insertedMoney
            )
        );
    }

    private function retrievePaymentSession(string $paymentSessionId): PaymentSessionResponse
    {
        return $this->queryBus->ask(
            new RetrievePaymentSessionQuery(
                self::VENDING_MACHINE_ID,
                $paymentSessionId
            )
        );
    }
}
