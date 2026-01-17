<?php

namespace App\Tests\unit\VendingMachine\PaymentSessions\Application\CancelPaymentSession;

use App\VendingMachine\PaymentSessions\Application\CancelPaymentSession\PaymentSessionCancelerService;
use App\VendingMachine\PaymentSessions\Domain\Exception\PaymentSessionDoesNotExistException;
use App\VendingMachine\PaymentSessions\Domain\PaymentSession;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionId;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionRepository;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PaymentSessionCancelerServiceTest extends KernelTestCase
{
    private PaymentSessionCancelerService $sut;
    private PaymentSessionRepository|MockObject $paymentSessionRepository;

    protected function setUp(): void
    {
        $this->paymentSessionRepository = $this->createMock(PaymentSessionRepository::class);

        $this->sut = new PaymentSessionCancelerService(
            $this->paymentSessionRepository
        );
    }

    #[Test]
    public function itShouldCancelAPaymentSession(): void
    {
        $this->paymentSessionRepository->expects($this->once())->method('id')->willReturn(
            new PaymentSession(
                PaymentSessionId::random(),
                VendingMachineId::random(),
                new ArrayCollection()
            )
        );

        $this->paymentSessionRepository->expects($this->once())->method('delete');

        ($this->sut)(
            PaymentSessionId::random()
        );
    }

    #[Test]
    public function itShouldThrowExceptionIfPaymentSessionDoNotExist(): void
    {
        $this->expectException(PaymentSessionDoesNotExistException::class);

        $this->paymentSessionRepository->expects($this->once())->method('id')->willReturn(null);

        $this->paymentSessionRepository->expects($this->never())->method('delete');

        ($this->sut)(
            PaymentSessionId::random()
        );
    }
}
