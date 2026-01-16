<?php

namespace App\Console;

use App\Shared\Domain\Bus\Command\CommandBus;
use App\Shared\Domain\Bus\Query\QueryBus;
use App\Shared\Domain\Exception\DomainException;
use App\VendingMachine\PaymentSessions\Application\InsertMoney\InsertMoneyCommand;
use App\VendingMachine\PaymentSessions\Application\RetrievePaymentSession\RetrievePaymentSessionQuery;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
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

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $this->commandBus->dispatch(
                new InsertMoneyCommand(
                    self::VENDING_MACHINE_ID,
                    self::PAYMENT_SESSION_ID,
                    0.25
                )
            );

            $paymentSession = $this->queryBus->ask(
                new RetrievePaymentSessionQuery(
                    self::VENDING_MACHINE_ID,
                    self::PAYMENT_SESSION_ID
                )
            );

            $output->writeln(sprintf('Current balance: %s', $paymentSession->totalInsertedMoney()));
            return Command::SUCCESS;
        } catch (DomainException $exception) {
            $output->writeln($exception->getMessage());
            return Command::FAILURE;
        }
    }
}
