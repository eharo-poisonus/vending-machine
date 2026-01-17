<?php

namespace App\Console;

use App\Shared\Domain\Bus\Command\CommandBus;
use App\Shared\Domain\Bus\Query\QueryBus;
use App\Shared\Domain\Exception\DomainException;
use App\VendingMachine\PaymentSessions\Application\CancelPaymentSession\CancelPaymentSessionCommand;
use App\VendingMachine\PaymentSessions\Application\CompletePaymentSession\CompletePaymentSessionCommand;
use App\VendingMachine\PaymentSessions\Application\RetrievePaymentSession\PaymentSessionCurrencyResponse;
use App\VendingMachine\PaymentSessions\Application\RetrievePaymentSession\RetrievePaymentSessionQuery;
use Symfony\Component\Console\Attribute\Argument;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'vending-machine:buy-product')]
class BuyProductConsoleCommand extends BaseConsoleCommand
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly CommandBus $commandBus,
        ?string $name = null,
        ?callable $code = null
    ) {
        parent::__construct($name, $code);
    }

    public function __invoke(
        #[Argument('The product code')] string $productCode,
        InputInterface $input,
        OutputInterface $output
    ): int {
        try {
            $sessionId = $this->retrievePaymentSessionIdFromMemory();

            if (null === $sessionId) {
                $output->writeln('No payment session found.');
                return Command::FAILURE;
            }

            $this->commandBus->dispatch(
                new CompletePaymentSessionCommand(
                    self::VENDING_MACHINE_ID,
                    $sessionId,
                    $productCode
                )
            );

            $changeMoney = $this->queryBus->ask(
                new RetrievePaymentSessionQuery($sessionId)
            );

            $output->writeln($this->changeToString($changeMoney->insertedCurrencies()));

            $this->commandBus->dispatch(new CancelPaymentSessionCommand($sessionId));
            $this->removePaymentSessionIdFromMemory();

            return Command::SUCCESS;
        } catch (DomainException $exception) {
            $output->writeln($exception->getMessage());
            return Command::FAILURE;
        }
    }

    private function changeToString(array $insertedCoins): string
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
