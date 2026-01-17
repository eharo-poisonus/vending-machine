<?php

namespace App\Console;

use App\Shared\Domain\Bus\Command\CommandBus;
use App\Shared\Domain\Bus\Query\QueryBus;
use App\Shared\Domain\Bus\Query\Response;
use App\Shared\Domain\Exception\DomainException;
use App\VendingMachine\VendingMachines\Application\RetrieveVendingMachine\RetrieveVendingMachineQuery;
use App\VendingMachine\VendingMachines\Application\RetrieveVendingMachine\VendingMachineResponse;
use App\VendingMachine\VendingMachines\Application\UpdateVendingMachine\UpdateVendingMachineCommand;
use RuntimeException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'vending-machine:maintenance')]
class VendingMachineMaintenanceConsoleCommand extends BaseConsoleCommand
{
    public function __construct(
        private readonly QueryBus $queryBus,
        private readonly CommandBus $commandBus,
        ?string $name = null,
        ?callable $code = null
    ) {
        parent::__construct($name, $code);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            /** @var VendingMachineResponse $vendingMachine */
            $vendingMachine = $this->retrieveVendingMachine();
            $io = new SymfonyStyle($input, $output);

            $io->title('Maintenance Mode - Vending Machine');
            $updatedProducts = $this->askForProductSection($io, $vendingMachine->products());
            $updatedChangeCurrencies = $this->askForChangeCurrenciesSection($io, $vendingMachine->storedMoney());

            $this->updateVendingMachine($vendingMachine->id(), $updatedProducts, $updatedChangeCurrencies);

            $io->success('Vending machine updated successfully!');

            return Command::SUCCESS;
        } catch (DomainException $exception) {
            $output->writeln($exception->getMessage());
            return Command::FAILURE;
        }
    }

    private function retrieveVendingMachine(): Response
    {
        return $this->queryBus->ask(
            new RetrieveVendingMachineQuery(self::VENDING_MACHINE_ID)
        );
    }

    private function askForProductSection(SymfonyStyle $io, array $products): array
    {
        $io->section('Update Products');

        $updatedProducts = [];

        foreach ($products as $product) {
            $currentStock = $product->stock();
            $addStock = $io->ask(
                "{$product->name()} (current stock: {$currentStock}) - How many to add?",
                0,
                fn($value) => is_numeric($value) && $value >= 0 ? (int)$value : throw new RuntimeException(
                    'Invalid number'
                )
            );

            if ($addStock !== 0) {
                $updatedProducts[$product->id()] = $addStock;
            }
        }

        return $updatedProducts;
    }

    private function askForChangeCurrenciesSection(SymfonyStyle $io, array $changeCurrencies): array
    {
        $io->section('Update Change / Coins');

        $updatedChangeStorage = [];

        foreach ($changeCurrencies as $currency) {
            $currentAmount = $currency->amount();
            $addAmount = $io->ask(
                "{$currency->value()}$ (current amount: {$currentAmount}) - How many to add?",
                0,
                fn($value) => is_numeric($value) && $value >= 0 ? (int)$value : throw new RuntimeException(
                    'Invalid number'
                )
            );

            if ($addAmount !== 0) {
                $updatedChangeStorage[$currency->id()] = $addAmount;
            }
        }

        return $updatedChangeStorage;
    }

    private function updateVendingMachine(
        string $vendingMachineId,
        array $updatedProducts,
        array $updatedChangeCurrencies
    ): void {
        $this->commandBus->dispatch(
            new UpdateVendingMachineCommand(
                $vendingMachineId,
                $updatedProducts,
                $updatedChangeCurrencies
            )
        );
    }
}
