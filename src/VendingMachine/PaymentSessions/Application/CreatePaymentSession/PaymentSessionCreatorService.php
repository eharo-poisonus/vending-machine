<?php

namespace App\VendingMachine\PaymentSessions\Application\CreatePaymentSession;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Filter\Filter;
use App\Shared\Domain\Criteria\Group\FiltersGroupAnd;
use App\Shared\Domain\Exception\NotAllowedCurrencyDenominationException;
use App\Shared\Domain\ValueObject\Money;
use App\VendingMachine\PaymentSessions\Domain\Exception\PaymentSessionAlreadyExists;
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

final readonly class PaymentSessionCreatorService
{
    public function __construct(
        private PaymentSessionRepository $paymentSessionRepository,
        private VendingMachineRepository $vendingMachineRepository,
        private CurrencyDenominationRepository $currencyDenominationRepository
    ) {
    }

    /** @throws NotAllowedCurrencyDenominationException|VendingMachineDoesNotExistException|PaymentSessionAlreadyExists */
    public function __invoke(
        VendingMachineId $vendingMachineId,
        PaymentSessionId $paymentSessionId,
        Money $insertedMoney
    ): void {
        $vendingMachine = $this->vendingMachineRepository->id($vendingMachineId);
        $this->ensureVendingMachineExists($vendingMachine);

        $activePaymentSessions = $this->paymentSessionRepository->search(
            $this->searchActivePaymentSessionsCriteria($vendingMachineId)
        );
        $this->ensureVendingMachineDoesNotHaveActivePaymentSessions($activePaymentSessions);

        $insertedCurrencyDenomination = $this->retrieveInsertedCurrencyDenomination($insertedMoney);

        $paymentSession = $this->createPaymentSession($vendingMachineId, $paymentSessionId);

        $paymentSession->addCurrency($insertedCurrencyDenomination);

        $this->paymentSessionRepository->save($paymentSession);
    }

    /** @throws VendingMachineDoesNotExistException */
    private function ensureVendingMachineExists(?VendingMachine $vendingMachine): void
    {
        if (null === $vendingMachine) {
            throw new VendingMachineDoesNotExistException();
        }
    }

    private function searchActivePaymentSessionsCriteria(VendingMachineId $vendingMachineId): Criteria
    {
        return Criteria::create(
            filtersGroups: [
                FiltersGroupAnd::fromValues([
                    Filter::fromValues([
                        'field' => 'vendingMachineId',
                        'operator' => '=',
                        'value' => $vendingMachineId,
                    ]),
                ])
            ]
        );
    }

    /** @throws PaymentSessionAlreadyExists */
    private function ensureVendingMachineDoesNotHaveActivePaymentSessions(array $activePaymentSessions): void
    {
        if (!empty($activePaymentSessions)) {
            throw new PaymentSessionAlreadyExists();
        }
    }

    /** @throws NotAllowedCurrencyDenominationException */
    private function retrieveInsertedCurrencyDenomination(Money $insertedMoney): CurrencyDenomination
    {
        $currencyDenominations = $this->currencyDenominationRepository->search(
            $this->searchDenominationByCentsValue($insertedMoney)
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

    private function createPaymentSession(
        VendingMachineId $vendingMachineId,
        PaymentSessionId $paymentSessionId
    ): PaymentSession {
        return new PaymentSession(
            $paymentSessionId,
            $vendingMachineId,
            new ArrayCollection()
        );
    }
}
