<?php

namespace App\Tests\unit\PaymentSessions\Application\RetrievePaymentSession;

use App\VendingMachine\PaymentSessions\Application\RetrievePaymentSession\PaymentSessionRetrieverService;
use App\VendingMachine\PaymentSessions\Domain\Exception\PaymentSessionDoesNotExistException;
use App\VendingMachine\PaymentSessions\Domain\PaymentSession;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionId;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionRepository;
use App\VendingMachine\VendingMachines\Domain\Exception\VendingMachineDoesNotExistException;
use App\VendingMachine\VendingMachines\Domain\VendingMachine;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;
use App\VendingMachine\VendingMachines\Domain\VendingMachineRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PaymentSessionRetrieverServiceTest extends KernelTestCase
{
    private PaymentSessionRetrieverService $sut;
    private VendingMachineRepository|MockObject $vendingMachineRepository;
    private PaymentSessionRepository|MockObject $paymentSessionRepository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->vendingMachineRepository = $this->createMock(VendingMachineRepository::class);
        $this->paymentSessionRepository = $this->createMock(PaymentSessionRepository::class);

        $this->sut = new PaymentSessionRetrieverService(
            $this->paymentSessionRepository,
            $this->vendingMachineRepository
        );
    }

    #[Test]
    public function itShouldRetrieveAPaymentSession(): void
    {
        $vendingMachineId = VendingMachineId::random();
        $paymentSessionId = PaymentSessionId::random();

        $this->vendingMachineRepository->expects($this->once())->method('id')->willReturn(
            new VendingMachine(
                $vendingMachineId,
                true,
                new ArrayCollection(),
                new ArrayCollection(),
                new DateTimeImmutable()
            )
        );

        $this->paymentSessionRepository->expects($this->once())->method('id')->willReturn(
            new PaymentSession(
                $paymentSessionId,
                $vendingMachineId,
                new ArrayCollection()
            )
        );

        ($this->sut)(
            VendingMachineId::random(),
            PaymentSessionId::random()
        );
    }

    #[Test]
    public function itShouldThrowExceptionOnVendingMachineNotFound(): void
    {
        $this->expectException(VendingMachineDoesNotExistException::class);

        $this->vendingMachineRepository->expects($this->once())->method('id')->willReturn(null);

        $this->paymentSessionRepository->expects($this->never())->method('id');

        ($this->sut)(
            VendingMachineId::random(),
            PaymentSessionId::random()
        );
    }

    #[Test]
    public function itShouldThrowExceptionOnPaymentSessionNotFound(): void
    {
        $vendingMachineId = VendingMachineId::random();

        $this->expectException(PaymentSessionDoesNotExistException::class);

        $this->vendingMachineRepository->expects($this->once())->method('id')->willReturn(
            new VendingMachine(
                $vendingMachineId,
                true,
                new ArrayCollection(),
                new ArrayCollection(),
                new DateTimeImmutable()
            )
        );

        $this->paymentSessionRepository->expects($this->once())->method('id')->willReturn(null);

        ($this->sut)(
            VendingMachineId::random(),
            PaymentSessionId::random()
        );
    }
}
