<?php

namespace App\VendingMachine\PaymentSessions\Application\RetrievePaymentSessionPurchase;

use App\Shared\Domain\Bus\Query\QueryHandler;
use App\Shared\Domain\Exception\InvalidUuidException;
use App\VendingMachine\PaymentSessions\Domain\Exception\PaymentSessionDoesNotExistException;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionId;
use App\VendingMachine\VendingMachines\Domain\Exception\ProductDoesNotExistException;
use App\VendingMachine\VendingMachines\Domain\Exception\VendingMachineDoesNotExistException;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;

final readonly class RetrievePaymentSessionPurchaseQueryHandler implements QueryHandler
{
    public function __construct(
        private PaymentSessionPurchaseRetrieverService $service
    ) {
    }

    /**
     * @throws VendingMachineDoesNotExistException|PaymentSessionDoesNotExistException|InvalidUuidException
     * @throws ProductDoesNotExistException
     */
    public function __invoke(RetrievePaymentSessionPurchaseQuery $query): PurchaseResponse
    {
        return ($this->service)(
            VendingMachineId::fromString($query->vendingMachineId()),
            PaymentSessionId::fromString($query->paymentSessionId()),
            $query->productCode()
        );
    }
}
