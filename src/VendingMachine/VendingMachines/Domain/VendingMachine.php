<?php

namespace App\VendingMachine\VendingMachines\Domain;

use App\Shared\Domain\Aggregate\AggregateRoot;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;

class VendingMachine extends AggregateRoot
{
    public function __construct(
        private VendingMachineId $id,
        private Collection $products,
        private Collection $moneyInventory,
        private DateTimeImmutable $installedAt
    ) {
    }

    public function id(): VendingMachineId
    {
        return $this->id;
    }

    public function setId(VendingMachineId $id): void
    {
        $this->id = $id;
    }

    public function installedAt(): DateTimeImmutable
    {
        return $this->installedAt;
    }

    public function setInstalledAt(DateTimeImmutable $installedAt): void
    {
        $this->installedAt = $installedAt;
    }

    public function products(): Collection
    {
        return $this->products;
    }

    public function setProducts(Collection $products): void
    {
        $this->products = $products;
    }

    public function moneyInventory(): Collection
    {
        return $this->moneyInventory;
    }

    public function setMoneyInventory(Collection $moneyInventory): void
    {
        $this->moneyInventory = $moneyInventory;
    }
}
