<?php

namespace App\Console;

use App\Shared\Domain\Bus\Command\CommandBus;
use App\Shared\Domain\Bus\Query\QueryBus;
use App\Shared\Domain\Exception\DomainException;
use App\VendingMachine\PaymentSessions\Application\CancelPaymentSession\CancelPaymentSessionCommand;
use App\VendingMachine\PaymentSessions\Application\RetrievePaymentSession\PaymentSessionCurrencyResponse;
use App\VendingMachine\PaymentSessions\Application\RetrievePaymentSession\PaymentSessionResponse;
use App\VendingMachine\PaymentSessions\Application\RetrievePaymentSession\RetrievePaymentSessionQuery;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'vending-machine:return-money')]
class ReturnMoneyConsoleCommand extends BaseConsoleCommand
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
            $actualPaymentSessionId = $this->retrievePaymentSessionIdFromMemory();

            if (null === $actualPaymentSessionId) {
                $output->writeln('No payment session found.');
                return Command::FAILURE;
            }

            $paymentSession = $this->retrievePaymentSession($actualPaymentSessionId);

            $this->cancelPaymentSession($actualPaymentSessionId);

            $output->writeln(
                sprintf('Returned coins: %s', $this->insertedCoinsToString($paymentSession->insertedCurrencies()))
            );

            return Command::SUCCESS;
        } catch (DomainException $exception) {
            $output->writeln($exception->getMessage());
            return Command::FAILURE;
        }
    }

    private function retrievePaymentSession(string $paymentSessionId): PaymentSessionResponse
    {
        return $this->queryBus->ask(
            new RetrievePaymentSessionQuery(
                $paymentSessionId
            )
        );
    }

    private function cancelPaymentSession(string $paymentSessionId): void
    {
        $this->commandBus->dispatch(
            new CancelPaymentSessionCommand(
                $paymentSessionId
            )
        );

        $this->removePaymentSessionIdFromMemory();
    }

    private function insertedCoinsToString(array $insertedCoins): string
    {
        $normalizedInsertedCoins = array_merge(
            ...array_map(
                fn(PaymentSessionCurrencyResponse $currency) => array_fill(
                    0,
                    $currency->amount(),
                    $currency->value()
                ),
                $insertedCoins
            )
        );
        return implode(', ', $normalizedInsertedCoins);
    }
}
