<?php

namespace App\VendingMachine\PaymentSessions\Application\RetrievePaymentSessionPurchase;

use App\VendingMachine\PaymentSessions\Domain\Exception\PaymentSessionDoesNotExistException;
use App\VendingMachine\PaymentSessions\Domain\PaymentSession;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionId;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionRepository;
use App\VendingMachine\VendingMachines\Domain\Exception\ProductDoesNotExistException;
use App\VendingMachine\VendingMachines\Domain\Exception\VendingMachineDoesNotExistException;
use App\VendingMachine\VendingMachines\Domain\VendingMachine;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;
use App\VendingMachine\VendingMachines\Domain\VendingMachineRepository;

final readonly class PaymentSessionPurchaseRetrieverService
{
    public function __construct(
        private VendingMachineRepository $vendingMachineRepository,
        private PaymentSessionRepository $paymentSessionRepository
    ) {
    }

    /** @throws VendingMachineDoesNotExistException|PaymentSessionDoesNotExistException|ProductDoesNotExistException */
    public function __invoke(
        VendingMachineId $vendingMachineId,
        PaymentSessionId $paymentSessionId,
        string $productCode
    ): PurchaseResponse {
        $vendingMachine = $this->vendingMachineRepository->id($vendingMachineId);
        $this->ensureVendingMachineExists($vendingMachine);

        $paymentSession = $this->paymentSessionRepository->id($paymentSessionId);
        $this->ensurePaymentSessionExists($paymentSession);

        $purchasedProduct = $vendingMachine->productByCode($productCode);

        return PurchaseResponse::fromProductAndChangeCurrencies(
            $purchasedProduct->name(),
            $paymentSession->insertedCurrencies()
        );
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
}
