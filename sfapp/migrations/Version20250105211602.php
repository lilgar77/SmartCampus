<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20250105211602 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE alert (id INT AUTO_INCREMENT NOT NULL, id_sa_id INT DEFAULT NULL, id_room_id INT DEFAULT NULL, date_begin DATETIME NOT NULL, date_end DATETIME DEFAULT NULL, type VARCHAR(255) NOT NULL, INDEX IDX_17FD46C18C4FA076 (id_sa_id), INDEX IDX_17FD46C18A8AD9E3 (id_room_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE alert ADD CONSTRAINT FK_17FD46C18C4FA076 FOREIGN KEY (id_sa_id) REFERENCES acquisition_system (id)');
        $this->addSql('ALTER TABLE alert ADD CONSTRAINT FK_17FD46C18A8AD9E3 FOREIGN KEY (id_room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE alert DROP FOREIGN KEY FK_17FD46C18C4FA076');
        $this->addSql('ALTER TABLE alert DROP FOREIGN KEY FK_17FD46C18A8AD9E3');
        $this->addSql('DROP TABLE alert');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }
}
