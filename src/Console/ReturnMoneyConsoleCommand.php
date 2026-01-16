<?php

namespace App\Console;

use App\Shared\Domain\Bus\Command\CommandBus;
use App\Shared\Domain\Bus\Query\QueryBus;
use App\Shared\Domain\Exception\DomainException;
use App\VendingMachine\PaymentSessions\Application\CancelPaymentSession\CancelPaymentSessionCommand;
use App\VendingMachine\PaymentSessions\Application\RetrievePaymentSession\PaymentSessionCurrencyResponse;
use App\VendingMachine\PaymentSessions\Application\RetrievePaymentSession\PaymentSessionResponse;
use App\VendingMachine\PaymentSessions\Application\RetrievePaymentSession\RetrievePaymentSessionQuery;
use App\VendingMachine\PaymentSessions\Domain\PaymentSession;
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
            /** @var PaymentSessionResponse $paymentSession */
            $paymentSession = $this->queryBus->ask(
                new RetrievePaymentSessionQuery(
                    self::VENDING_MACHINE_ID,
                    self::PAYMENT_SESSION_ID
                )
            );

            $insertedCoins = $paymentSession->insertedCurrencies();

            $this->commandBus->dispatch(
                new CancelPaymentSessionCommand(
                    self::VENDING_MACHINE_ID,
                    self::PAYMENT_SESSION_ID
                )
            );

            $output->writeln(sprintf('Returned coins: %s', $this->insertedCoinsToString($insertedCoins)));

            return Command::SUCCESS;
        } catch (DomainException $exception) {
            $output->writeln($exception->getMessage());
            return Command::FAILURE;
        }
    }

    private function insertedCoinsToString(array $insertedCoins): string
    {
        $normalizedInsertedCoins = array_merge(
            ...array_map(
                fn (PaymentSessionCurrencyResponse $currency) => array_fill(
                    0, $currency->amount(), $currency->value()
                ),
                $insertedCoins
            )
        );
        return implode(', ', $normalizedInsertedCoins);
    }
}
