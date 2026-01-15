<?php

declare(strict_types=1);

namespace Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260115120239 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create products table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE IF NOT EXISTS products (
                id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
                vending_machine_id INT NOT NULL,
                code VARCHAR(100) NOT NULL,
                price_in_cents INT NOT NULL,
                stock INT NOT NULL,
                CONSTRAINT FK_products_vending_machine
                    FOREIGN KEY (vending_machine_id)
                    REFERENCES vending_machines(id)
                    ON DELETE CASCADE
                    ON UPDATE CASCADE,
                UNIQUE INDEX unique_product_per_machine (vending_machine_id, code),
                INDEX idx_product_code (code)
            )
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS products');
    }
}
