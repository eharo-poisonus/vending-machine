<?php

namespace App\VendingMachines\Domain;

use App\Shared\Domain\Aggregate\AggregateRoot;
use DateTimeImmutable;
use Doctrine\Common\Collections\Collection;

class VendingMachine extends AggregateRoot
{
    public function __construct(
        private int $id,
        private bool $active,
        private DateTimeImmutable $installedAt,
        private DateTimeImmutable $lastService,
        private DateTimeImmutable $lastMaintenance,
        private Collection $products,
        private Collection $moneyInventory
    ) {
    }

    public function id(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function installedAt(): DateTimeImmutable
    {
        return $this->installedAt;
    }

    public function setInstalledAt(DateTimeImmutable $installedAt): void
    {
        $this->installedAt = $installedAt;
    }

    public function lastService(): DateTimeImmutable
    {
        return $this->lastService;
    }

    public function setLastService(DateTimeImmutable $lastService): void
    {
        $this->lastService = $lastService;
    }

    public function lastMaintenance(): DateTimeImmutable
    {
        return $this->lastMaintenance;
    }

    public function setLastMaintenance(DateTimeImmutable $lastMaintenance): void
    {
        $this->lastMaintenance = $lastMaintenance;
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
