<?php

namespace App\VendingMachine\PaymentSessions\Application\CancelPaymentSession;

use App\VendingMachine\PaymentSessions\Domain\Exception\PaymentSessionDoesNotExistException;
use App\VendingMachine\PaymentSessions\Domain\PaymentSession;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionId;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionRepository;

final readonly class PaymentSessionCancelerService
{
    public function __construct(
        private PaymentSessionRepository $paymentSessionRepository
    ) {
    }

    /** @throws PaymentSessionDoesNotExistException */
    public function __invoke(PaymentSessionId $paymentSessionId): void
    {
        $paymentSession = $this->paymentSessionRepository->id($paymentSessionId);
        $this->ensurePaymentSessionExists($paymentSession);

        $this->paymentSessionRepository->delete($paymentSession);
    }

    /** @throws PaymentSessionDoesNotExistException */
    private function ensurePaymentSessionExists(?PaymentSession $paymentSession): void
    {
        if (null === $paymentSession) {
            throw new PaymentSessionDoesNotExistException();
        }
    }
}
