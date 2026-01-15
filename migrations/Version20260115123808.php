<?php

declare(strict_types=1);

namespace Doctrine\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20260115123808 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'create currency_denominations table';
    }

    public function up(Schema $schema): void
    {
        $this->addSql('
            CREATE TABLE IF NOT EXISTS currency_denominations (
                id INT AUTO_INCREMENT NOT NULL PRIMARY KEY,
                value_in_cents INT NOT NULL
            )
        ');
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE IF EXISTS currency_denominations');
    }
}
