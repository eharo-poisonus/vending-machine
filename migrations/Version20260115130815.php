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
        $this->addSql('
            INSERT INTO vending_machines (id, installed_at) VALUES (\'db4463a3-6e45-4bd6-ba32-b2b9cc27c7ac\', NOW())
        ');

        $this->addSql('
            INSERT INTO products (id, vending_machine_id, name, code, price_in_cents, stock)
            VALUES (\''.SimpleUuid::random().'\', \'db4463a3-6e45-4bd6-ba32-b2b9cc27c7ac\', \'WATER\', \'GET-WATER\', 65, 5),
                   (\''.SimpleUuid::random().'\', \'db4463a3-6e45-4bd6-ba32-b2b9cc27c7ac\', \'JUICE\', \'GET-JUICE\', 100, 5),
                   (\''.SimpleUuid::random().'\', \'db4463a3-6e45-4bd6-ba32-b2b9cc27c7ac\', \'SODA\', \'GET-SODA\', 150, 5)
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
            VALUES (1, \'db4463a3-6e45-4bd6-ba32-b2b9cc27c7ac\', 1, 10),
                   (2, \'db4463a3-6e45-4bd6-ba32-b2b9cc27c7ac\', 2, 10),
                   (3, \'db4463a3-6e45-4bd6-ba32-b2b9cc27c7ac\', 3, 10),
                   (4, \'db4463a3-6e45-4bd6-ba32-b2b9cc27c7ac\', 4, 10)
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
