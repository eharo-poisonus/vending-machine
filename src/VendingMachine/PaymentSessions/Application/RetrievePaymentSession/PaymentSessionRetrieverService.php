<?php

namespace App\VendingMachine\PaymentSessions\Application\RetrievePaymentSession;

use App\VendingMachine\PaymentSessions\Domain\Exception\PaymentSessionDoesNotExistException;
use App\VendingMachine\PaymentSessions\Domain\PaymentSession;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionId;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionRepository;

final readonly class PaymentSessionRetrieverService
{
    public function __construct(
        private PaymentSessionRepository $paymentSessionRepository
    ) {
    }

    /** @throws PaymentSessionDoesNotExistException */
    public function __invoke(PaymentSessionId $paymentSessionId): PaymentSession
    {
        $paymentSession = $this->paymentSessionRepository->id($paymentSessionId);
        $this->ensurePaymentSessionExists($paymentSession);

        return $paymentSession;
    }

    /** @throws PaymentSessionDoesNotExistException */
    private function ensurePaymentSessionExists(?PaymentSession $paymentSession): void
    {
        if (null === $paymentSession) {
            throw new PaymentSessionDoesNotExistException();
        }
    }
}
