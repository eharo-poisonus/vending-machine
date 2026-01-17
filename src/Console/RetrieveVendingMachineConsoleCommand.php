<?php

namespace App\Console;

use App\Shared\Domain\Bus\Query\QueryBus;
use App\Shared\Domain\Exception\DomainException;
use App\VendingMachine\VendingMachines\Application\RetrieveVendingMachine\RetrieveVendingMachineQuery;
use App\VendingMachine\VendingMachines\Application\RetrieveVendingMachine\VendingMachineResponse;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'vending-machine:retrieve')]
class RetrieveVendingMachineConsoleCommand extends BaseConsoleCommand
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
            /** @var VendingMachineResponse $vendingMachine */
            $vendingMachine = $this->queryBus->ask(
                new RetrieveVendingMachineQuery(
                    self::VENDING_MACHINE_ID
                )
            );

            $this->printLegibleVendingMachine($input, $output, $vendingMachine);

            return Command::SUCCESS;
        } catch (DomainException $exception) {
            $output->writeln($exception->getMessage());
            return Command::FAILURE;
        }
    }

    private function printLegibleVendingMachine(
        InputInterface $input,
        OutputInterface $output,
        VendingMachineResponse $vendingMachine
    ): void {
        $io = new SymfonyStyle($input, $output);

        $io->title('Vending Machine Info');
        $io->writeln('ID: ' . $vendingMachine->id());
        $io->writeln('Installed At: ' . $vendingMachine->installedAt());

        $io->section('Products');
        $productsTable = [];
        foreach ($vendingMachine->products() as $product) {
            $productsTable[] = [
                $product->id(),
                $product->name(),
                $product->code(),
                $product->price(),
                $product->stock(),
            ];
        }
        $io->table(['ID', 'Name', 'Code', 'Price', 'Stock'], $productsTable);

        $io->section('Stored Money / Change Inventory');
        $moneyTable = [];
        foreach ($vendingMachine->storedMoney() as $currency) {
            $moneyTable[] = [
                number_format($currency->value(), 2),
                $currency->amount(),
            ];
        }
        $io->table(['Value', 'Amount'], $moneyTable);
    }
}
