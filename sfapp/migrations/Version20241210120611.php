<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241210120611 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE installation (id INT AUTO_INCREMENT NOT NULL, room_id INT DEFAULT NULL, as_id INT DEFAULT NULL, comment VARCHAR(255) DEFAULT NULL, INDEX IDX_1CBA6AB154177093 (room_id), INDEX IDX_1CBA6AB182FABAB0 (as_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE installation ADD CONSTRAINT FK_1CBA6AB154177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('ALTER TABLE installation ADD CONSTRAINT FK_1CBA6AB182FABAB0 FOREIGN KEY (as_id) REFERENCES acquisition_system (id)');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE installation DROP FOREIGN KEY FK_1CBA6AB154177093');
        $this->addSql('ALTER TABLE installation DROP FOREIGN KEY FK_1CBA6AB182FABAB0');
        $this->addSql('DROP TABLE installation');
        $this->addSql('ALTER TABLE user CHANGE roles roles JSON NOT NULL COMMENT \'(DC2Type:json)\'');
    }
}
