<?php

namespace App\VendingMachine\PaymentSessions\Application\CompletePaymentSession;

use App\Shared\Domain\ValueObject\Money;
use App\VendingMachine\PaymentSessions\Domain\Exception\NotEnoughMoneyException;
use App\VendingMachine\PaymentSessions\Domain\Exception\PaymentSessionDoesNotExistException;
use App\VendingMachine\PaymentSessions\Domain\PaymentSession;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionCurrency;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionId;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionRepository;
use App\VendingMachine\VendingMachines\Domain\Exception\ProductOutOfStockException;
use App\VendingMachine\VendingMachines\Domain\Exception\VendingMachineDoesNotExistException;
use App\VendingMachine\VendingMachines\Domain\MachineChangeCurrency;
use App\VendingMachine\VendingMachines\Domain\Product;
use App\VendingMachine\VendingMachines\Domain\VendingMachine;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;
use App\VendingMachine\VendingMachines\Domain\VendingMachineRepository;

final readonly class PaymentSessionCompleterService
{
    public function __construct(
        private VendingMachineRepository $vendingMachineRepository,
        private PaymentSessionRepository $paymentSessionRepository
    ) {
    }

    public function __invoke(
        VendingMachineId $vendingMachineId,
        PaymentSessionId $paymentSessionId,
        string $productCode
    ): void {
        $vendingMachine = $this->vendingMachineRepository->id($vendingMachineId);
        $this->ensureVendingMachineExists($vendingMachine);

        $paymentSession = $this->paymentSessionRepository->id($paymentSessionId);
        $this->ensurePaymentSessionExists($paymentSession);

        $selectedProduct = $vendingMachine->productByCode($productCode);
        $this->ensureProductHasStock($selectedProduct);
        $this->ensureProductIsAffordable($paymentSession, $selectedProduct->price());

        $vendingMachine->collectMoney($paymentSession->insertedCurrencies());

        $changeCurrencies = $vendingMachine->calculateChange($paymentSession->total(), $selectedProduct->price());
        $vendingMachine->dispenseChange($changeCurrencies);

        $vendingMachine->dispenseProduct($selectedProduct);

        $paymentSession->replaceInsertedCurrencies(
            $this->mapChangeForPaymentSessionCurrency($paymentSession, $changeCurrencies)
        );

        $this->paymentSessionRepository->update($paymentSession);
        $this->vendingMachineRepository->update($vendingMachine);
    }

    /** @throws VendingMachineDoesNotExistException */
    private function ensureVendingMachineExists(?VendingMachine $vendingMachine): void
    {
        if ($vendingMachine === null) {
            throw new VendingMachineDoesNotExistException();
        }
    }

    /** @throws PaymentSessionDoesNotExistException */
    private function ensurePaymentSessionExists(?PaymentSession $paymentSession): void
    {
        if (null === $paymentSession) {
            throw new PaymentSessionDoesNotExistException();
        }
    }

    /** @throws ProductOutOfStockException */
    private function ensureProductHasStock(Product $product): void
    {
        if (!$product->hasStock()) {
            throw new ProductOutOfStockException();
        }
    }

    /** @throws NotEnoughMoneyException */
    private function ensureProductIsAffordable(PaymentSession $paymentSession, Money $productPrice): void
    {
        if (!$paymentSession->canAfford($productPrice)) {
            throw new NotEnoughMoneyException();
        }
    }

    private function mapChangeForPaymentSessionCurrency(
        PaymentSession $paymentSession,
        array $machineChangeCurrencies
    ): array {
        return array_map(
            fn(MachineChangeCurrency $machineChangeCurrency) => new PaymentSessionCurrency(
                $paymentSession,
                $machineChangeCurrency->denomination(),
                $machineChangeCurrency->amount()
            ),
            $machineChangeCurrencies
        );
    }
}
