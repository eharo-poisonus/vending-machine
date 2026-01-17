<?php

namespace App\Tests\unit\VendingMachine\PaymentSessions\Application\RetrievePaymentSession;

use App\VendingMachine\PaymentSessions\Application\RetrievePaymentSession\PaymentSessionRetrieverService;
use App\VendingMachine\PaymentSessions\Domain\Exception\PaymentSessionDoesNotExistException;
use App\VendingMachine\PaymentSessions\Domain\PaymentSession;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionId;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionRepository;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PaymentSessionRetrieverServiceTest extends KernelTestCase
{
    private PaymentSessionRetrieverService $sut;
    private PaymentSessionRepository|MockObject $paymentSessionRepository;

    protected function setUp(): void
    {
        $this->paymentSessionRepository = $this->createMock(PaymentSessionRepository::class);

        $this->sut = new PaymentSessionRetrieverService(
            $this->paymentSessionRepository
        );
    }

    #[Test]
    public function itShouldRetrieveAPaymentSession(): void
    {
        $paymentSessionId = PaymentSessionId::random();

        $this->paymentSessionRepository->expects($this->once())->method('id')->willReturn(
            new PaymentSession(
                $paymentSessionId,
                VendingMachineId::random(),
                new ArrayCollection()
            )
        );

        ($this->sut)(
            PaymentSessionId::random()
        );
    }

    #[Test]
    public function itShouldThrowExceptionOnPaymentSessionNotFound(): void
    {
        $this->expectException(PaymentSessionDoesNotExistException::class);

        $this->paymentSessionRepository->expects($this->once())->method('id')->willReturn(null);

        ($this->sut)(
            PaymentSessionId::random()
        );
    }
}
