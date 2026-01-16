<?php

namespace App\VendingMachine\PaymentSessions\Application\InsertMoney;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Filter\Filter;
use App\Shared\Domain\Criteria\Group\FiltersGroupAnd;
use App\Shared\Domain\Exception\NotAllowedCurrencyDenominationException;
use App\Shared\Domain\ValueObject\Money;
use App\VendingMachine\PaymentSessions\Domain\Exception\MultiplePaymentSessionsActiveException;
use App\VendingMachine\PaymentSessions\Domain\PaymentSession;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionId;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionRepository;
use App\VendingMachine\Shared\Domain\CurrencyDenomination;
use App\VendingMachine\Shared\Domain\CurrencyDenominationRepository;
use App\VendingMachine\VendingMachines\Domain\Exception\VendingMachineDoesNotExistException;
use App\VendingMachine\VendingMachines\Domain\VendingMachine;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;
use App\VendingMachine\VendingMachines\Domain\VendingMachineRepository;
use Doctrine\Common\Collections\ArrayCollection;

final readonly class MoneyInserterService
{
    public function __construct(
        private PaymentSessionRepository $paymentSessionRepository,
        private VendingMachineRepository $vendingMachineRepository,
        private CurrencyDenominationRepository $currencyDenominationRepository
    ) {
    }

    /** @throws MultiplePaymentSessionsActiveException|VendingMachineDoesNotExistException|NotAllowedCurrencyDenominationException */
    public function __invoke(VendingMachineId $vendingMachineId, PaymentSessionId $paymentSessionId, Money $money): void
    {
        $vendingMachine = $this->vendingMachineRepository->id($vendingMachineId);
        $this->ensureVendingMachineExists($vendingMachine);

        $activePaymentSession = $this->retrieveActivePaymentSession($vendingMachineId);

        $insertedCurrencyDenomination = $this->retrieveInsertedCurrencyDenomination($money);

        $paymentSession = $this->returnOrCreatePaymentSession(
            $activePaymentSession,
            $paymentSessionId,
            $vendingMachineId
        );

        $paymentSession->addCurrency($insertedCurrencyDenomination);

        $this->paymentSessionRepository->save($paymentSession);
    }

    /** @throws MultiplePaymentSessionsActiveException */
    private function retrieveActivePaymentSession(VendingMachineId $vendingMachineId): ?PaymentSession
    {
        $activePaymentSessions = $this->paymentSessionRepository->search(
            $this->searchActiveSessionsCriteria($vendingMachineId)
        );
        $this->ensureVendingMachineHasNoneOrOneSessionActive($activePaymentSessions);

        return array_shift($activePaymentSessions);
    }

    public function searchActiveSessionsCriteria(VendingMachineId $vendingMachineId): Criteria
    {
        return Criteria::create([
            FiltersGroupAnd::fromValues([
                Filter::fromValues([
                    'field' => 'vendingMachineId',
                    'operator' => '=',
                    'value' => $vendingMachineId
                ])
            ])
        ]);
    }

    /** @throws NotAllowedCurrencyDenominationException */
    private function retrieveInsertedCurrencyDenomination(Money $money): CurrencyDenomination
    {
        $currencyDenominations = $this->currencyDenominationRepository->search(
            $this->searchDenominationByCentsValue($money)
        );
        $this->ensureMoneyInsertedIsValid($currencyDenominations);

        return array_shift($currencyDenominations);
    }

    public function searchDenominationByCentsValue(Money $money): Criteria
    {
        return Criteria::create(
            filtersGroups: [
                FiltersGroupAnd::fromValues([
                    Filter::fromValues([
                        'field' => 'money',
                        'operator' => '=',
                        'value' => $money,
                    ])
                ])
            ],
            limit: 1
        );
    }

    private function returnOrCreatePaymentSession(
        ?PaymentSession $paymentSession,
        PaymentSessionId $paymentSessionId,
        VendingMachineId $vendingMachineId
    ): PaymentSession {
        if (null !== $paymentSession) {
            return $paymentSession;
        }

        return new PaymentSession(
            $paymentSessionId,
            $vendingMachineId,
            new ArrayCollection()
        );
    }

    /** @throws VendingMachineDoesNotExistException */
    private function ensureVendingMachineExists(?VendingMachine $vendingMachine): void
    {
        if (null === $vendingMachine) {
            throw new VendingMachineDoesNotExistException();
        }
    }

    /** @throws MultiplePaymentSessionsActiveException */
    private function ensureVendingMachineHasNoneOrOneSessionActive(array $activeSessions): void
    {
        if (count($activeSessions) > 1) {
            throw new MultiplePaymentSessionsActiveException();
        }
    }

    /**
     * @param CurrencyDenomination[] $currencyDenominations
     * @throws NotAllowedCurrencyDenominationException
     */
    private function ensureMoneyInsertedIsValid(array $currencyDenominations): void
    {
        if (empty($currencyDenominations)) {
            throw new NotAllowedCurrencyDenominationException();
        }
    }
}
