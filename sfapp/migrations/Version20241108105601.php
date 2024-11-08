<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241108105601 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE acquisition_system (id INT AUTO_INCREMENT NOT NULL, temperature INT NOT NULL, co2 INT NOT NULL, humidity INT NOT NULL, wording VARCHAR(255) NOT NULL, mac_adress VARCHAR(255) NOT NULL, etat VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('CREATE TABLE room (id INT AUTO_INCREMENT NOT NULL, name VARCHAR(255) NOT NULL, id_as_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_729F519B6C7FFB80 (id_as_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B6C7FFB80 FOREIGN KEY (id_as_id) REFERENCES acquisition_system (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B6C7FFB80');
        $this->addSql('DROP TABLE acquisition_system');
        $this->addSql('DROP TABLE room');
    }
}
