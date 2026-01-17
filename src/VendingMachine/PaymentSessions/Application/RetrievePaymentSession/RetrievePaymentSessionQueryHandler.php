<?php

namespace App\VendingMachine\PaymentSessions\Application\RetrievePaymentSession;

use App\Shared\Domain\Bus\Query\QueryHandler;
use App\Shared\Domain\Exception\InvalidUuidException;
use App\VendingMachine\PaymentSessions\Domain\Exception\PaymentSessionDoesNotExistException;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionId;

final readonly class RetrievePaymentSessionQueryHandler implements QueryHandler
{
     public function __construct(
         private PaymentSessionRetrieverService $service
     ) {
     }

    /** @throws InvalidUuidException|PaymentSessionDoesNotExistException */
    public function __invoke(RetrievePaymentSessionQuery $query): PaymentSessionResponse
     {
         $paymentSession = ($this->service)(
             PaymentSessionId::fromString($query->paymentSessionId())
         );

         return PaymentSessionResponse::fromPaymentSession($paymentSession);
     }
}
