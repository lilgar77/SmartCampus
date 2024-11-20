<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241115104122 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE building (id INT AUTO_INCREMENT NOT NULL, name_building VARCHAR(255) NOT NULL, adress_building VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE floor (id INT AUTO_INCREMENT NOT NULL, id_building_id INT NOT NULL, number_floor INT NOT NULL, INDEX IDX_BE45D62E5538B3E5 (id_building_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE floor ADD CONSTRAINT FK_BE45D62E5538B3E5 FOREIGN KEY (id_building_id) REFERENCES building (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE acquisition_system ADD name VARCHAR(255) DEFAULT NULL, CHANGE macAdress mac_adress VARCHAR(255) NOT NULL');
        $this->addSql('ALTER TABLE room ADD floor_id INT NOT NULL, ADD building_id INT NOT NULL');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B854679E2 FOREIGN KEY (floor_id) REFERENCES floor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B4D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id) ON DELETE CASCADE');
        $this->addSql('CREATE INDEX IDX_729F519B854679E2 ON room (floor_id)');
        $this->addSql('CREATE INDEX IDX_729F519B4D2A7E12 ON room (building_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B4D2A7E12');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B854679E2');
        $this->addSql('ALTER TABLE floor DROP FOREIGN KEY FK_BE45D62E5538B3E5');
        $this->addSql('DROP TABLE building');
        $this->addSql('DROP TABLE floor');
        $this->addSql('DROP INDEX IDX_729F519B854679E2 ON room');
        $this->addSql('DROP INDEX IDX_729F519B4D2A7E12 ON room');
        $this->addSql('ALTER TABLE room DROP floor_id, DROP building_id');
        $this->addSql('ALTER TABLE acquisition_system DROP name, CHANGE mac_adress macAdress VARCHAR(255) NOT NULL');
    }
}
