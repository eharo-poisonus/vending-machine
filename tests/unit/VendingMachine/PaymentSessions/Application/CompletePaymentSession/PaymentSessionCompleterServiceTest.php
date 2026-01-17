<?php

namespace App\Tests\unit\VendingMachine\PaymentSessions\Application\CompletePaymentSession;

use App\Shared\Domain\ValueObject\Money;
use App\VendingMachine\PaymentSessions\Application\CompletePaymentSession\PaymentSessionCompleterService;
use App\VendingMachine\PaymentSessions\Domain\Exception\NotEnoughMoneyException;
use App\VendingMachine\PaymentSessions\Domain\Exception\PaymentSessionDoesNotExistException;
use App\VendingMachine\PaymentSessions\Domain\PaymentSession;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionCurrency;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionId;
use App\VendingMachine\PaymentSessions\Domain\PaymentSessionRepository;
use App\VendingMachine\Shared\Domain\CurrencyDenomination;
use App\VendingMachine\VendingMachines\Domain\Exception\ProductOutOfStockException;
use App\VendingMachine\VendingMachines\Domain\Exception\VendingMachineDoesNotExistException;
use App\VendingMachine\VendingMachines\Domain\MachineChangeCurrency;
use App\VendingMachine\VendingMachines\Domain\Product;
use App\VendingMachine\VendingMachines\Domain\ProductId;
use App\VendingMachine\VendingMachines\Domain\VendingMachine;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;
use App\VendingMachine\VendingMachines\Domain\VendingMachineRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class PaymentSessionCompleterServiceTest extends KernelTestCase
{
    private PaymentSessionCompleterService $sut;
    private VendingMachineRepository|MockObject $vendingMachineRepository;
    private PaymentSessionRepository|MockObject $paymentSessionRepository;

    protected function setUp(): void
    {
        $this->vendingMachineRepository = $this->createMock(VendingMachineRepository::class);
        $this->paymentSessionRepository = $this->createMock(PaymentSessionRepository::class);

        $this->sut = new PaymentSessionCompleterService(
            $this->vendingMachineRepository,
            $this->paymentSessionRepository
        );
    }

    #[Test]
    public function itShouldCompleteAPaymentSession(): void
    {
        $vendingMachineId = VendingMachineId::random();
        $paymentSessionId = PaymentSessionId::random();
        $productCode = 'GET-JUICE';

        $vendingMachine = $this->createVendingMachine($vendingMachineId);
        $paymentSession = $this->createPaymentSession($vendingMachineId, $paymentSessionId);

        $paymentSession->addCurrency(new CurrencyDenomination(4, Money::fromCents(100)));
        $paymentSession->addCurrency(new CurrencyDenomination(4, Money::fromCents(100)));

        $this->vendingMachineRepository->expects($this->once())
            ->method('id')
            ->with($vendingMachineId)
            ->willReturn($vendingMachine);

        $this->paymentSessionRepository->expects($this->once())
            ->method('id')
            ->with($paymentSessionId)
            ->willReturn($paymentSession);

        $this->paymentSessionRepository->expects($this->once())
            ->method('update')
            ->with($paymentSession);

        $this->vendingMachineRepository->expects($this->once())
            ->method('update')
            ->with($vendingMachine);

        ($this->sut)($vendingMachineId, $paymentSessionId, $productCode);

        $this->assertCount(1, $paymentSession->insertedCurrencies());

        /** @var PaymentSessionCurrency $changeCurrency */
        $changeCurrency = $paymentSession->insertedCurrencies()->first();
        $this->assertEquals(100, $changeCurrency->denomination()->money()->cents());
        $this->assertEquals(1, $changeCurrency->amount());

        $this->assertEquals(0, $vendingMachine->productByCode($productCode)->stock());
    }

    #[Test]
    public function itShouldThrowExceptionWhenVendingMachineDoesNotExist(): void
    {
        $vendingMachineId = VendingMachineId::random();
        $paymentSessionId = PaymentSessionId::random();
        $productCode = 'GET-JUICE';

        $this->vendingMachineRepository->expects($this->once())
            ->method('id')
            ->with($vendingMachineId)
            ->willReturn(null);

        $this->paymentSessionRepository->expects($this->never())
            ->method('update');

        $this->vendingMachineRepository->expects($this->never())
            ->method('update');

        $this->expectException(VendingMachineDoesNotExistException::class);

        ($this->sut)($vendingMachineId, $paymentSessionId, $productCode);
    }

    #[Test]
    public function itShouldThrowExceptionWhenPaymentSessionDoesNotExist(): void
    {
        $vendingMachineId = VendingMachineId::random();
        $paymentSessionId = PaymentSessionId::random();
        $productCode = 'GET-JUICE';

        $vendingMachine = $this->createVendingMachine($vendingMachineId);

        $this->vendingMachineRepository->expects($this->once())
            ->method('id')
            ->with($vendingMachineId)
            ->willReturn($vendingMachine);

        $this->paymentSessionRepository->expects($this->once())
            ->method('id')
            ->with($paymentSessionId)
            ->willReturn(null);

        $this->paymentSessionRepository->expects($this->never())
            ->method('update');

        $this->vendingMachineRepository->expects($this->never())
            ->method('update');

        $this->expectException(PaymentSessionDoesNotExistException::class);

        ($this->sut)($vendingMachineId, $paymentSessionId, $productCode);
    }

    #[Test]
    public function itShouldThrowExceptionWhenProductOutOfStock(): void
    {
        $vendingMachineId = VendingMachineId::random();
        $paymentSessionId = PaymentSessionId::random();
        $productCode = 'GET-JUICE';

        $vendingMachine = $this->createVendingMachine($vendingMachineId);
        $vendingMachine->productByCode($productCode)->setStock(0);

        $paymentSession = $this->createPaymentSession($vendingMachineId, $paymentSessionId);

        $this->vendingMachineRepository->expects($this->once())
            ->method('id')
            ->with($vendingMachineId)
            ->willReturn($vendingMachine);

        $this->paymentSessionRepository->expects($this->once())
            ->method('id')
            ->with($paymentSessionId)
            ->willReturn($paymentSession);

        $this->paymentSessionRepository->expects($this->never())
            ->method('update');

        $this->vendingMachineRepository->expects($this->never())
            ->method('update');

        $this->expectException(ProductOutOfStockException::class);

        ($this->sut)($vendingMachineId, $paymentSessionId, $productCode);
    }

    #[Test]
    public function itShouldThrowExceptionWhenNotEnoughMoney(): void
    {
        $vendingMachineId = VendingMachineId::random();
        $paymentSessionId = PaymentSessionId::random();
        $productCode = 'GET-JUICE';

        $vendingMachine = $this->createVendingMachine($vendingMachineId);
        $paymentSession = $this->createPaymentSession($vendingMachineId, $paymentSessionId);

        $paymentSession->addCurrency(new CurrencyDenomination(3, Money::fromCents(50)));

        $this->vendingMachineRepository->expects($this->once())
            ->method('id')
            ->with($vendingMachineId)
            ->willReturn($vendingMachine);

        $this->paymentSessionRepository->expects($this->once())
            ->method('id')
            ->with($paymentSessionId)
            ->willReturn($paymentSession);

        $this->paymentSessionRepository->expects($this->never())
            ->method('update');

        $this->vendingMachineRepository->expects($this->never())
            ->method('update');

        $this->expectException(NotEnoughMoneyException::class);

        $this->sut->__invoke($vendingMachineId, $paymentSessionId, $productCode);
    }

    private function createVendingMachine(VendingMachineId $vendingMachineId): VendingMachine
    {
        $vendingMachine = new VendingMachine(
            $vendingMachineId,
            new ArrayCollection(),
            new ArrayCollection(),
            new DateTimeImmutable()
        );

        $vendingMachine->setProducts(
            new ArrayCollection([
                new Product(
                    ProductId::fromString('1fe2d17c-0976-44fb-b25f-b32cda0a3c88'),
                    $vendingMachine,
                    'JUICE',
                    'GET-JUICE',
                    Money::fromCents(100),
                    1
                ),
                new Product(
                    ProductId::fromString('3895d86c-0ddf-42ac-a1e8-3de114ac297f'),
                    $vendingMachine,
                    'WATER',
                    'GET-WATER',
                    Money::fromCents(65),
                    2
                ),
                new Product(
                    ProductId::fromString('6fd73d89-71d0-42d3-87b9-316d0fa9e291'),
                    $vendingMachine,
                    'SODA',
                    'GET-SODA',
                    Money::fromCents(150),
                    3
                )
            ])
        );

        $vendingMachine->setMoneyInventory(
            new ArrayCollection([
                new MachineChangeCurrency(
                    1,
                    $vendingMachine,
                    new CurrencyDenomination(1, Money::fromCents(5)),
                    15
                ),
                new MachineChangeCurrency(
                    2,
                    $vendingMachine,
                    new CurrencyDenomination(2, Money::fromCents(10)),
                    16
                ),
                new MachineChangeCurrency(
                    3,
                    $vendingMachine,
                    new CurrencyDenomination(3, Money::fromCents(25)),
                    17
                ),
                new MachineChangeCurrency(
                    4,
                    $vendingMachine,
                    new CurrencyDenomination(4, Money::fromCents(100)),
                    18
                )
            ])
        );

        return $vendingMachine;
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
