<?php

namespace App\Tests\unit\PaymentSessions\Application\InsertMoney;

use App\Shared\Domain\Exception\NotAllowedCurrencyDenominationException;
use App\Shared\Domain\ValueObject\Money;
use App\VendingMachine\PaymentSessions\Application\InsertMoney\MoneyInserterService;
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
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class MoneyInserterServiceTest extends TestCase
{
    private MoneyInserterService $sut;
    private PaymentSessionRepository|MockObject $paymentSessionRepository;
    private VendingMachineRepository|MockObject $vendingMachineRepository;
    private CurrencyDenominationRepository|MockObject $currencyDenominationRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->paymentSessionRepository = $this->createMock(PaymentSessionRepository::class);
        $this->vendingMachineRepository = $this->createMock(VendingMachineRepository::class);
        $this->currencyDenominationRepository = $this->createMock(CurrencyDenominationRepository::class);

        $this->sut = new MoneyInserterService(
            $this->paymentSessionRepository,
            $this->vendingMachineRepository,
            $this->currencyDenominationRepository
        );
    }

    #[Test]
    public function itShouldCreateAPaymentSession(): void
    {
        $this->vendingMachineRepository->expects($this->once())->method('id')->willReturn(
            new VendingMachine(
                VendingMachineId::random(),
                true,
                new ArrayCollection(),
                new ArrayCollection(),
                new DateTimeImmutable()
            )
        );

        $this->paymentSessionRepository->expects($this->once())->method('search')->willReturn([]);

        $this->currencyDenominationRepository->expects($this->once())->method('search')->willReturn([
            new CurrencyDenomination(1, Money::fromCents(10))
        ]);

        $this->paymentSessionRepository->expects($this->once())->method('save');

        ($this->sut)(
            VendingMachineId::random(),
            PaymentSessionId::random(),
            Money::fromCents(10)
        );
    }

    #[Test]
    public function itShouldThrowExceptionIfVendingMachineDoNotExist(): void
    {
        $this->expectException(VendingMachineDoesNotExistException::class);

        $this->vendingMachineRepository->expects($this->once())->method('id')->willReturn(null);

        $this->paymentSessionRepository->expects($this->never())->method('search');

        $this->currencyDenominationRepository->expects($this->never())->method('search');

        $this->paymentSessionRepository->expects($this->never())->method('save');

        ($this->sut)(
            VendingMachineId::random(),
            PaymentSessionId::random(),
            Money::fromCents(10)
        );
    }

    #[Test]
    public function itShouldThrowExceptionIfOtherSessionIsRunning(): void
    {
        $this->expectException(MultiplePaymentSessionsActiveException::class);

        $this->vendingMachineRepository->expects($this->once())->method('id')->willReturn(
            new VendingMachine(
                VendingMachineId::random(),
                true,
                new ArrayCollection(),
                new ArrayCollection(),
                new DateTimeImmutable()
            )
        );

        $this->paymentSessionRepository->expects($this->once())->method('search')->willReturn([
            new PaymentSession(
                PaymentSessionId::random(),
                VendingMachineId::random(),
                new ArrayCollection()
            ),
            new PaymentSession(
                PaymentSessionId::random(),
                VendingMachineId::random(),
                new ArrayCollection()
            )
        ]);

        $this->currencyDenominationRepository->expects($this->never())->method('search');

        $this->paymentSessionRepository->expects($this->never())->method('save');

        ($this->sut)(
            VendingMachineId::random(),
            PaymentSessionId::random(),
            Money::fromCents(10)
        );
    }

    #[Test]
    public function itShouldThrowExceptionOnInvalidDenominationInserted(): void
    {
        $this->expectException(NotAllowedCurrencyDenominationException::class);

        $this->vendingMachineRepository->expects($this->once())->method('id')->willReturn(
            new VendingMachine(
                VendingMachineId::random(),
                true,
                new ArrayCollection(),
                new ArrayCollection(),
                new DateTimeImmutable()
            )
        );

        $this->paymentSessionRepository->expects($this->once())->method('search')->willReturn([]);

        $this->currencyDenominationRepository->expects($this->once())->method('search')->willReturn([]);

        $this->paymentSessionRepository->expects($this->never())->method('save');

        ($this->sut)(
            VendingMachineId::random(),
            PaymentSessionId::random(),
            Money::fromCents(10)
        );
    }
}
