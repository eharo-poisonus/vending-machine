<?php

namespace App\VendingMachine\PaymentSessions\Application\RetrievePaymentSession;

use App\Shared\Domain\Bus\Query\QueryHandler;
use App\Shared\Domain\Exception\InvalidUuidException;
use App\VendingMachine\PaymentSessions\Domain\Exception\PaymentSessionDoesNotExistException;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionId;
use App\VendingMachine\VendingMachines\Domain\Exception\VendingMachineDoesNotExistException;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;

final readonly class RetrievePaymentSessionQueryHandler implements QueryHandler
{
     public function __construct(
         private PaymentSessionRetrieverService $service
     ) {
     }

    /** @throws VendingMachineDoesNotExistException|InvalidUuidException|PaymentSessionDoesNotExistException */
    public function __invoke(RetrievePaymentSessionQuery $query): PaymentSessionResponse
     {
         $paymentSession = ($this->service)(
             VendingMachineId::fromString($query->vendingMachineId()),
             PaymentSessionId::fromString($query->paymentSessionId())
         );

         return PaymentSessionResponse::fromPaymentSession($paymentSession);
     }
}
