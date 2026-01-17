<?php

namespace App\VendingMachine\PaymentSessions\Application\UpdatePaymentService;

use App\Shared\Domain\Criteria\Criteria;
use App\Shared\Domain\Criteria\Filter\Filter;
use App\Shared\Domain\Criteria\Group\FiltersGroupAnd;
use App\Shared\Domain\Exception\NotAllowedCurrencyDenominationException;
use App\Shared\Domain\ValueObject\Money;
use App\VendingMachine\PaymentSessions\Domain\Exception\PaymentSessionDoesNotExistException;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionId;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionRepository;
use App\VendingMachine\Shared\Domain\CurrencyDenomination;
use App\VendingMachine\Shared\Domain\CurrencyDenominationRepository;

final readonly class PaymentSessionUpdaterService
{
    public function __construct(
        private PaymentSessionRepository $paymentSessionRepository,
        private CurrencyDenominationRepository $currencyDenominationRepository
    ) {
    }

    /** @throws NotAllowedCurrencyDenominationException|PaymentSessionDoesNotExistException */
    public function __invoke(PaymentSessionId $paymentSessionId, Money $insertedMoney): void
    {
        $actualPaymentSession = $this->paymentSessionRepository->id($paymentSessionId);
        $this->ensurePaymentSessionExists($actualPaymentSession);

        $insertedCurrencyDenomination = $this->retrieveInsertedCurrencyDenomination($insertedMoney);

        $actualPaymentSession->addCurrency($insertedCurrencyDenomination);

        $this->paymentSessionRepository->save($actualPaymentSession);
    }

    /** @throws PaymentSessionDoesNotExistException */
    private function ensurePaymentSessionExists(?object $actualPaymentSession): void
    {
        if (null === $actualPaymentSession) {
            throw new PaymentSessionDoesNotExistException();
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
}
