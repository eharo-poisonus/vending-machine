<?php

declare(strict_types=1);

namespace Doctrine\Migrations;

use App\Shared\Domain\ValueObject\SimpleUuid;
use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260115130815 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Populate tables with data';
    }

    public function up(Schema $schema): void
    {
        $vendingMachineId = SimpleUuid::random();

        $this->addSql('
            INSERT INTO vending_machines (id, is_active, installed_at) VALUES (\''.$vendingMachineId.'\', 1, NOW())
        ');

        $this->addSql('
            INSERT INTO products (id, vending_machine_id, code, price_in_cents, stock)
            VALUES (\''.SimpleUuid::random().'\', \''.$vendingMachineId.'\', \'GET_WATER\', 65, 5),
                   (\''.SimpleUuid::random().'\', \''.$vendingMachineId.'\', \'GET_JUICE\', 100, 5),
                   (\''.SimpleUuid::random().'\', \''.$vendingMachineId.'\', \'GET_SODA\', 150, 5)
        ');

        $this->addSql('
            INSERT INTO currency_denominations (id, value_in_cents)
            VALUES (1, 5),
                   (2, 10),
                   (3, 25),
                   (4, 100)
        ');

        $this->addSql('
            INSERT INTO machine_currencies (id, vending_machine_id, denomination_id, amount)
            VALUES (1, \''.$vendingMachineId.'\', 1, 10),
                   (2, \''.$vendingMachineId.'\', 2, 10),
                   (3, \''.$vendingMachineId.'\', 3, 10),
                   (4, \''.$vendingMachineId.'\', 4, 10)
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM machine_currencies');

        $this->addSql('DELETE FROM products');

        $this->addSql('DELETE FROM vending_machines');

        $this->addSql('DELETE FROM currency_denominations');
    }
}
