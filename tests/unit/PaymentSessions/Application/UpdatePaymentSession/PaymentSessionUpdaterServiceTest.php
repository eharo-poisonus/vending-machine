<?php

namespace App\Tests\unit\PaymentSessions\Application\UpdatePaymentSession;

use App\Shared\Domain\Exception\NotAllowedCurrencyDenominationException;
use App\Shared\Domain\ValueObject\Money;
use App\VendingMachine\PaymentSessions\Application\UpdatePaymentService\PaymentSessionUpdaterService;
use App\VendingMachine\PaymentSessions\Domain\Exception\PaymentSessionDoesNotExistException;
use App\VendingMachine\PaymentSessions\Domain\PaymentSession;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionId;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionRepository;
use App\VendingMachine\Shared\Domain\CurrencyDenomination;
use App\VendingMachine\Shared\Domain\CurrencyDenominationRepository;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PaymentSessionUpdaterServiceTest extends KernelTestCase
{
    private PaymentSessionUpdaterService $sut;
    private PaymentSessionRepository|MockObject $paymentSessionRepository;
    private CurrencyDenominationRepository|MockObject $currencyDenominationRepository;

    protected function setUp(): void
    {
        $this->paymentSessionRepository = $this->createMock(PaymentSessionRepository::class);
        $this->currencyDenominationRepository = $this->createMock(CurrencyDenominationRepository::class);

        $this->sut = new PaymentSessionUpdaterService(
            $this->paymentSessionRepository,
            $this->currencyDenominationRepository
        );
    }

    #[Test]
    public function itShouldUpdateAPaymentSession(): void
    {
        $paymentSessionId = PaymentSessionId::random();
        $insertedMoney = Money::fromCents(10);

        $this->paymentSessionRepository->expects($this->once())
            ->method('id')
            ->willReturn(
                new PaymentSession(
                    $paymentSessionId,
                    VendingMachineId::random(),
                    new ArrayCollection())
            );

        $this->currencyDenominationRepository->expects($this->once())
            ->method('search')
            ->willReturn([
                new CurrencyDenomination(1, $insertedMoney)
            ]);

        $this->paymentSessionRepository->expects($this->once())
            ->method('save');

        ($this->sut)(
            $paymentSessionId,
            $insertedMoney
        );
    }

    #[Test]
    public function itShouldThrowExceptionIfPaymentSessionDoNotExist(): void
    {
        $paymentSessionId = PaymentSessionId::random();
        $insertedMoney = Money::fromCents(10);

        $this->expectException(PaymentSessionDoesNotExistException::class);

        $this->paymentSessionRepository->expects($this->once())
            ->method('id')
            ->willReturn(null);

        $this->currencyDenominationRepository->expects($this->never())
            ->method('search');

        $this->paymentSessionRepository->expects($this->never())
            ->method('save');

        ($this->sut)(
            $paymentSessionId,
            $insertedMoney
        );
    }

    #[Test]
    public function itShouldThrowExceptionIfCurrencyDenominationDoNotExist(): void
    {
        $paymentSessionId = PaymentSessionId::random();
        $insertedMoney = Money::fromCents(10);

        $this->expectException(NotAllowedCurrencyDenominationException::class);

        $this->paymentSessionRepository->expects($this->once())
            ->method('id')
            ->willReturn(
                new PaymentSession(
                    $paymentSessionId,
                    VendingMachineId::random(),
                    new ArrayCollection())
            );

        $this->currencyDenominationRepository->expects($this->once())
            ->method('search')
            ->willReturn([]);

        $this->paymentSessionRepository->expects($this->never())
            ->method('save');

        ($this->sut)(
            $paymentSessionId,
            $insertedMoney
        );
    }
}
