<?php

namespace App\Console;

use App\Shared\Domain\Bus\Command\CommandBus;
use App\Shared\Domain\Bus\Query\QueryBus;
use App\Shared\Domain\Exception\DomainException;
use App\VendingMachine\PaymentSessions\Application\CancelPaymentSession\CancelPaymentSessionCommand;
use App\VendingMachine\PaymentSessions\Application\CompletePaymentSession\CompletePaymentSessionCommand;
use App\VendingMachine\PaymentSessions\Application\RetrievePaymentSession\PaymentSessionCurrencyResponse;
use App\VendingMachine\PaymentSessions\Application\RetrievePaymentSessionPurchase\PurchaseResponse;
use App\VendingMachine\PaymentSessions\Application\RetrievePaymentSessionPurchase\RetrievePaymentSessionPurchaseQuery;
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

            /** @var PurchaseResponse $purchaseResponse */
            $purchaseResponse = $this->queryBus->ask(
                new RetrievePaymentSessionPurchaseQuery(
                    self::VENDING_MACHINE_ID,
                    $sessionId,
                    $productCode
                )
            );

            $this->commandBus->dispatch(new CancelPaymentSessionCommand($sessionId));

            $output->writeln($this->formatStringFromResponse($purchaseResponse));
            $this->removePaymentSessionIdFromMemory();

            return Command::SUCCESS;
        } catch (DomainException $exception) {
            $output->writeln($exception->getMessage());
            return Command::FAILURE;
        }
    }

    private function formatStringFromResponse(PurchaseResponse $purchaseResponse): string
    {
        $normalizedInsertedCoins = array_merge(
            ...array_map(
                fn(PaymentSessionCurrencyResponse $currency) => array_fill(
                    0,
                    $currency->amount(),
                    $currency->value()
                ),
                $purchaseResponse->changeCurrencies()
            )
        );

        return sprintf('%s | %s', implode(', ', $normalizedInsertedCoins), $purchaseResponse->product());
    }
}
