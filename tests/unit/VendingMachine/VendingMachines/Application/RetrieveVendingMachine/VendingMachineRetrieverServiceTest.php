<?php

namespace App\Tests\unit\VendingMachine\VendingMachines\Application\RetrieveVendingMachine;

use App\VendingMachine\VendingMachines\Application\RetrieveVendingMachine\VendingMachineRetrieverService;
use App\VendingMachine\VendingMachines\Domain\Exception\VendingMachineDoesNotExistException;
use App\VendingMachine\VendingMachines\Domain\VendingMachine;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;
use App\VendingMachine\VendingMachines\Domain\VendingMachineRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class VendingMachineRetrieverServiceTest extends KernelTestCase
{
    private VendingMachineRetrieverService $sut;
    private VendingMachineRepository|MockObject $vendingMachineRepository;

    protected function setUp(): void
    {
        $this->vendingMachineRepository = $this->createMock(VendingMachineRepository::class);

        $this->sut = new VendingMachineRetrieverService(
            $this->vendingMachineRepository
        );
    }

    #[Test]
    public function itShouldRetrieveAVendingMachine(): void
    {
        $vendingMachineId = VendingMachineId::random();

        $this->vendingMachineRepository->expects($this->once())
            ->method('id')
            ->willReturn(
                new VendingMachine(
                    $vendingMachineId,
                    new ArrayCollection(),
                    new ArrayCollection(),
                    new DateTimeImmutable()
                )
            );

        ($this->sut)(
            $vendingMachineId
        );
    }

    #[Test]
    public function itShouldThrowExceptionOnVendingMachineNotFound(): void
    {
        $this->expectException(VendingMachineDoesNotExistException::class);

        $this->vendingMachineRepository->expects($this->once())
            ->method('id')
            ->willReturn(null);

        ($this->sut)(
            VendingMachineId::random()
        );
    }
}
