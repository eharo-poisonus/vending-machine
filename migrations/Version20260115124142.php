<?php

declare(strict_types=1);

namespace Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260115124142 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create inserted_currencies table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE IF NOT EXISTS inserted_currencies (
                id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
                vending_machine_id INT NOT NULL,
                denomination_id INT NOT NULL,
                amount INT NOT NULL,
                CONSTRAINT FK_inserted_currencies_vending_machines
                    FOREIGN KEY (vending_machine_id)
                    REFERENCES vending_machines(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                CONSTRAINT FK_inserted_currencies_currency_denominations
                    FOREIGN KEY (denomination_id)
                    REFERENCES currency_denominations(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE
            )
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS inserted_currencies');
    }
}
