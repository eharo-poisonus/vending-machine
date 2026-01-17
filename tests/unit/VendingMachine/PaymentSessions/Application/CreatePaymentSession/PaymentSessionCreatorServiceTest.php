<?php

namespace App\Tests\unit\VendingMachine\PaymentSessions\Application\CreatePaymentSession;

use App\Shared\Domain\Exception\NotAllowedCurrencyDenominationException;
use App\Shared\Domain\ValueObject\Money;
use App\VendingMachine\PaymentSessions\Application\CreatePaymentSession\PaymentSessionCreatorService;
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
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PaymentSessionCreatorServiceTest extends KernelTestCase
{
    private PaymentSessionCreatorService $sut;
    private PaymentSessionRepository|MockObject $paymentSessionRepository;
    private VendingMachineRepository|MockObject $vendingMachineRepository;
    private CurrencyDenominationRepository|MockObject $currencyDenominationRepository;

    protected function setUp(): void
    {
        $this->paymentSessionRepository = $this->createMock(PaymentSessionRepository::class);
        $this->vendingMachineRepository = $this->createMock(VendingMachineRepository::class);
        $this->currencyDenominationRepository = $this->createMock(CurrencyDenominationRepository::class);

        $this->sut = new PaymentSessionCreatorService(
            $this->paymentSessionRepository,
            $this->vendingMachineRepository,
            $this->currencyDenominationRepository
        );
    }

    #[Test]
    public function itShouldCreateAPaymentSession(): void
    {
        $vendingMachineId = VendingMachineId::random();
        $insertedMoney = Money::fromCents(10);

        $this->vendingMachineRepository->expects($this->once())
            ->method('id')
            ->willReturn(
                new VendingMachine(
                    $vendingMachineId,
                    true,
                    new ArrayCollection(),
                    new ArrayCollection(),
                    new DateTimeImmutable()
                )
            );

        $this->paymentSessionRepository->expects($this->once())
            ->method('search')
            ->willReturn([]);

        $this->currencyDenominationRepository->expects($this->once())
            ->method('search')
            ->willReturn([
                new CurrencyDenomination(1, $insertedMoney)
            ]);

        $this->paymentSessionRepository->expects($this->once())
            ->method('save');

        ($this->sut)(
            $vendingMachineId,
            PaymentSessionId::random(),
            $insertedMoney
        );
    }

    #[Test]
    public function itShouldThrowExceptionIfVendingMachineDoNotExist(): void
    {
        $vendingMachineId = VendingMachineId::random();
        $insertedMoney = Money::fromCents(10);

        $this->expectException(VendingMachineDoesNotExistException::class);

        $this->vendingMachineRepository->expects($this->once())
            ->method('id')
            ->willReturn(null);

        $this->paymentSessionRepository->expects($this->never())
            ->method('search');

        $this->currencyDenominationRepository->expects($this->never())
            ->method('search');

        $this->paymentSessionRepository->expects($this->never())
            ->method('save');

        ($this->sut)(
            $vendingMachineId,
            PaymentSessionId::random(),
            $insertedMoney
        );
    }

    #[Test]
    public function itShouldThrowExceptionIfPaymentSessionAlreadyExist(): void
    {
        $vendingMachineId = VendingMachineId::random();
        $paymentSessionId = PaymentSessionId::random();
        $insertedMoney = Money::fromCents(10);

        $this->expectException(PaymentSessionAlreadyExists::class);

        $this->vendingMachineRepository->expects($this->once())
            ->method('id')
            ->willReturn(
                new VendingMachine(
                    $vendingMachineId,
                    true,
                    new ArrayCollection(),
                    new ArrayCollection(),
                    new DateTimeImmutable()
                )
            );

        $this->paymentSessionRepository->expects($this->once())
            ->method('search')
            ->willReturn([
                new PaymentSession($paymentSessionId, $vendingMachineId, new ArrayCollection())
            ]);

        $this->currencyDenominationRepository->expects($this->never())
            ->method('search');

        $this->paymentSessionRepository->expects($this->never())
            ->method('save');

        ($this->sut)(
            $vendingMachineId,
            PaymentSessionId::random(),
            $insertedMoney
        );
    }

    #[Test]
    public function itShouldThrowExceptionIfCurrencyDenominationDoNotExist(): void
    {
        $vendingMachineId = VendingMachineId::random();
        $insertedMoney = Money::fromCents(10);

        $this->expectException(NotAllowedCurrencyDenominationException::class);

        $this->vendingMachineRepository->expects($this->once())
            ->method('id')
            ->willReturn(
                new VendingMachine(
                    $vendingMachineId,
                    true,
                    new ArrayCollection(),
                    new ArrayCollection(),
                    new DateTimeImmutable()
                )
            );

        $this->paymentSessionRepository->expects($this->once())
            ->method('search')
            ->willReturn([]);

        $this->currencyDenominationRepository->expects($this->once())
            ->method('search')
            ->willReturn([]);

        $this->paymentSessionRepository->expects($this->never())
            ->method('save');

        ($this->sut)(
            $vendingMachineId,
            PaymentSessionId::random(),
            $insertedMoney
        );
    }
}
