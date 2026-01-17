<?php

namespace App\Console;

use App\Shared\Domain\Bus\Query\QueryBus;
use App\Shared\Domain\Exception\DomainException;
use App\VendingMachine\PaymentSessions\Application\RetrievePaymentSession\PaymentSessionResponse;
use App\VendingMachine\PaymentSessions\Application\RetrievePaymentSession\RetrievePaymentSessionQuery;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'vending-machine:total-balance')]
class RetrievePaymentSessionConsoleCommand extends BaseConsoleCommand
{
    public function __construct(
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

            /** @var PaymentSessionResponse $paymentSession */
            $paymentSession = $this->queryBus->ask(
                new RetrievePaymentSessionQuery(
                    $actualPaymentSessionId
                )
            );

            $output->writeln(
                sprintf('Current balance: %s', $paymentSession->totalInsertedMoney())
            );

            return Command::SUCCESS;
        } catch (DomainException $exception) {
            $output->writeln($exception->getMessage());
            return Command::FAILURE;
        }

    }
}
