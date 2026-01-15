<?php

declare(strict_types=1);

namespace Doctrine\Migrations;

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
        $this->addSql('
            INSERT INTO vending_machines (id, is_active, installed_at) VALUES (1, 1, NOW())
        ');

        $this->addSql('
            INSERT INTO products (id, vending_machine_id, code, price_in_cents, stock)
            VALUES (1, 1, \'GET_WATER\', 65, 5),
                   (2, 1, \'GET_JUICE\', 100, 5),
                   (3, 1, \'GET_SODA\', 150, 5)
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
            VALUES (1, 1, 1, 10),
                   (2, 1, 2, 10),
                   (3, 1, 3, 10),
                   (4, 1, 4, 10)
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DELETE FROM machine_currencies WHERE vending_machine_id = 1');

        $this->addSql('DELETE FROM products WHERE vending_machine_id = 1');

        $this->addSql('DELETE FROM vending_machines WHERE id = 1');

        $this->addSql('DELETE FROM currency_denominations WHERE id IN (1,2,3,4)');
    }
}
