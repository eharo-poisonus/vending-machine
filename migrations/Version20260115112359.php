<?php

declare(strict_types=1);

namespace Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260115112359 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create vending machine table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE IF NOT EXISTS vending_machines (
                id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
                is_active TINYINT(1) NOT NULL,
                installed_at DATETIME NOT NULL,
                last_service DATETIME DEFAULT NULL,
                last_maintenance DATETIME DEFAULT NULL
            )
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('
            DROP TABLE IF EXISTS vending_machines
        ');
    }
}
