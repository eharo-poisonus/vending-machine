<?php

namespace App\Tests\unit\VendingMachine\VendingMachines\Application\UpdateVendingMachine;

use App\VendingMachine\VendingMachines\Application\UpdateVendingMachine\VendingMachineUpdaterService;
use App\VendingMachine\VendingMachines\Domain\Exception\VendingMachineDoesNotExistException;
use App\VendingMachine\VendingMachines\Domain\VendingMachine;
use App\VendingMachine\VendingMachines\Domain\VendingMachineId;
use App\VendingMachine\VendingMachines\Domain\VendingMachineRepository;
use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class VendingMachineUpdaterServiceTest extends KernelTestCase
{
    private VendingMachineUpdaterService $sut;
    private VendingMachineRepository|MockObject $vendingMachineRepository;

    protected function setUp(): void
    {
        $this->vendingMachineRepository = $this->createMock(VendingMachineRepository::class);

        $this->sut = new VendingMachineUpdaterService(
            $this->vendingMachineRepository
        );
    }

    #[Test]
    public function itShouldUpdateAVendingMachine(): void
    {
        $vendingMachine = new VendingMachine(
            VendingMachineId::random(),
            new ArrayCollection(),
            new ArrayCollection(),
            new DateTimeImmutable()
        );

        $this->vendingMachineRepository->expects($this->once())->method('id')->willReturn($vendingMachine);

        $this->vendingMachineRepository->expects($this->once())->method('update');

        ($this->sut)(VendingMachineId::random(), [], []);
    }

    #[Test]
    public function itShouldThrowExceptionOnVendingMachineNotFound(): void
    {
        $this->expectException(VendingMachineDoesNotExistException::class);

        $this->vendingMachineRepository->expects($this->once())->method('id')->willReturn(null);

        $this->vendingMachineRepository->expects($this->never())->method('update');

        ($this->sut)(VendingMachineId::random(), [], []);
    }
}
