<?php

namespace App\Tests\unit\VendingMachine\PaymentSessions\Application\RetrievePaymentSessionPurchase;

use App\Shared\Domain\ValueObject\Money;
use App\VendingMachine\PaymentSessions\Application\RetrievePaymentSessionPurchase\PaymentSessionPurchaseRetrieverService;
use App\VendingMachine\PaymentSessions\Domain\Exception\PaymentSessionDoesNotExistException;
use App\VendingMachine\PaymentSessions\Domain\PaymentSession;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionCurrency;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionId;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionRepository;
use App\VendingMachine\Shared\Domain\CurrencyDenomination;
use App\VendingMachine\VendingMachines\Domain\Exception\VendingMachineDoesNotExistException;
use App\VendingMachine\VendingMachines\Domain\Product;
use App\VendingMachine\VendingMachines\Domain\VendingMachine;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;
use App\VendingMachine\VendingMachines\Domain\VendingMachineRepository;
use App\VendingMachine\VendingMachines\Domain\ProductId;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PaymentSessionPurchaseRetrieverServiceTest extends KernelTestCase
{
    private PaymentSessionPurchaseRetrieverService $sut;
    private VendingMachineRepository|MockObject $vendingMachineRepository;
    private PaymentSessionRepository|MockObject $paymentSessionRepository;

    protected function setUp(): void
    {
        $this->vendingMachineRepository = $this->createMock(VendingMachineRepository::class);
        $this->paymentSessionRepository = $this->createMock(PaymentSessionRepository::class);

        $this->sut = new PaymentSessionPurchaseRetrieverService(
            $this->vendingMachineRepository,
            $this->paymentSessionRepository
        );
    }

    #[Test]
    public function itShouldRetrieveAPaymentSessionPurchase(): void
    {
        $vendingMachineId = VendingMachineId::random();
        $paymentSessionId = PaymentSessionId::random();
        $productCode = 'GET-WATER';
        $productName = 'Water';
        $money = Money::fromFloat(1.0);

        $vendingMachine = new VendingMachine(
            $vendingMachineId,
            new ArrayCollection(),
            new ArrayCollection(),
            new DateTimeImmutable()
        );
        $product = new Product(
            ProductId::random(),
            $vendingMachine,
            $productName,
            $productCode,
            $money,
            10
        );
        $vendingMachine->setProducts(new ArrayCollection([$product]));

        $paymentSession = new PaymentSession(
            $paymentSessionId,
            $vendingMachineId,
            new ArrayCollection()
        );
        $currencyDenomination = new CurrencyDenomination(1, $money);
        $paymentSessionCurrency = new PaymentSessionCurrency($paymentSession, $currencyDenomination, 1);
        $paymentSession->setInsertedCurrencies(new ArrayCollection([$paymentSessionCurrency]));

        $this->vendingMachineRepository->expects($this->once())
            ->method('id')
            ->with($vendingMachineId)
            ->willReturn($vendingMachine);

        $this->paymentSessionRepository->expects($this->once())->method('id')
            ->with($paymentSessionId)
            ->willReturn($paymentSession);

        $response = ($this->sut)($vendingMachineId, $paymentSessionId, $productCode);

        $this->assertEquals($productName, $response->product());
        $this->assertCount(1, $response->changeCurrencies());
        $this->assertEquals(1.0, $response->changeCurrencies()[0]->value());
        $this->assertEquals(1, $response->changeCurrencies()[0]->amount());
    }

    #[Test]
    public function itShouldThrowExceptionIfVendingMachineDoNotExist(): void
    {
        $vendingMachineId = VendingMachineId::random();
        $paymentSessionId = PaymentSessionId::random();
        $productCode = 'GET-WATER';

        $this->vendingMachineRepository->expects($this->once())
            ->method('id')
            ->with($vendingMachineId)
            ->willReturn(null);

        $this->paymentSessionRepository->expects($this->never())->method('id');

        $this->expectException(VendingMachineDoesNotExistException::class);

        ($this->sut)($vendingMachineId, $paymentSessionId, $productCode);
    }

    #[Test]
    public function itShouldThrowExceptionIfPaymentSessionDoNotExist(): void
    {
        $vendingMachineId = VendingMachineId::random();
        $paymentSessionId = PaymentSessionId::random();
        $productCode = 'GET-WATER';

        $vendingMachine = new VendingMachine(
            $vendingMachineId,
            new ArrayCollection(),
            new ArrayCollection(),
            new DateTimeImmutable()
        );

        $this->vendingMachineRepository->expects($this->once())
            ->method('id')
            ->with($vendingMachineId)
            ->willReturn($vendingMachine);

        $this->paymentSessionRepository->expects($this->once())
            ->method('id')
            ->with($paymentSessionId)
            ->willReturn(null);

        $this->expectException(PaymentSessionDoesNotExistException::class);

        ($this->sut)($vendingMachineId, $paymentSessionId, $productCode);
    }
}
