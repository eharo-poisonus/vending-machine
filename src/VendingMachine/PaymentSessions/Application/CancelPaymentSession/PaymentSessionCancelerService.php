<?php

namespace App\VendingMachine\PaymentSessions\Application\CancelPaymentSession;

use App\VendingMachine\PaymentSessions\Domain\Exception\PaymentSessionDoesNotExistException;
use App\VendingMachine\PaymentSessions\Domain\PaymentSession;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionId;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionRepository;
use App\VendingMachine\VendingMachines\Domain\Exception\VendingMachineDoesNotExistException;
use App\VendingMachine\VendingMachines\Domain\VendingMachine;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;
use App\VendingMachine\VendingMachines\Domain\VendingMachineRepository;

final readonly class PaymentSessionCancelerService
{
    public function __construct(
        private VendingMachineRepository $vendingMachineRepository,
        private PaymentSessionRepository $paymentSessionRepository
    ) {
    }

    /** @throws VendingMachineDoesNotExistException|PaymentSessionDoesNotExistException */
    public function __invoke(VendingMachineId $vendingMachineId, PaymentSessionId $paymentSessionId): void
    {
        $vendingMachine = $this->vendingMachineRepository->id($vendingMachineId);
        $this->ensureVendingMachineExists($vendingMachine);

        $paymentSession = $this->paymentSessionRepository->id($paymentSessionId);
        $this->ensurePaymentSessionExists($paymentSession);

        $this->paymentSessionRepository->delete($paymentSession);
    }

    /** @throws VendingMachineDoesNotExistException */
    private function ensureVendingMachineExists(?VendingMachine $vendingMachine): void
    {
        if (null === $vendingMachine) {
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
