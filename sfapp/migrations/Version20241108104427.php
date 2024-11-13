<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241108104427 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE room ADD id_as_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B6C7FFB80 FOREIGN KEY (id_as_id) REFERENCES acquisition_system (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_729F519B6C7FFB80 ON room (id_as_id)');
        $this->addSql('ALTER TABLE acquisition_system RENAME COLUMN mac_adress TO macAdress');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B6C7FFB80');
        $this->addSql('DROP INDEX UNIQ_729F519B6C7FFB80 ON room');
        $this->addSql('ALTER TABLE room DROP id_as_id');
    }
}
