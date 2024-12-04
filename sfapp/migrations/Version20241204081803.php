<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241204081803 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE archive (id INT AUTO_INCREMENT NOT NULL, acquisition_system_id INT NOT NULL, room_id INT NOT NULL, temperature DOUBLE PRECISION DEFAULT NULL, humidity DOUBLE PRECISION DEFAULT NULL, co2 INT DEFAULT NULL, date_capture DATETIME NOT NULL, INDEX IDX_D5FC5D9C331785FF (acquisition_system_id), INDEX IDX_D5FC5D9C54177093 (room_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE archive ADD CONSTRAINT FK_D5FC5D9C331785FF FOREIGN KEY (acquisition_system_id) REFERENCES acquisition_system (id)');
        $this->addSql('ALTER TABLE archive ADD CONSTRAINT FK_D5FC5D9C54177093 FOREIGN KEY (room_id) REFERENCES room (id)');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_13C61622EA5C2A4A ON acquisition_system (mac_adress)');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B6C7FFB80');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B854679E2 FOREIGN KEY (floor_id) REFERENCES floor (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B4D2A7E12 FOREIGN KEY (building_id) REFERENCES building (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B6C7FFB80 FOREIGN KEY (id_as_id) REFERENCES acquisition_system (id) ON DELETE SET NULL');
        $this->addSql('CREATE INDEX IDX_729F519B854679E2 ON room (floor_id)');
        $this->addSql('CREATE INDEX IDX_729F519B4D2A7E12 ON room (building_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE archive DROP FOREIGN KEY FK_D5FC5D9C331785FF');
        $this->addSql('ALTER TABLE archive DROP FOREIGN KEY FK_D5FC5D9C54177093');
        $this->addSql('DROP TABLE archive');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B854679E2');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B4D2A7E12');
        $this->addSql('ALTER TABLE room DROP FOREIGN KEY FK_729F519B6C7FFB80');
        $this->addSql('DROP INDEX IDX_729F519B854679E2 ON room');
        $this->addSql('DROP INDEX IDX_729F519B4D2A7E12 ON room');
        $this->addSql('ALTER TABLE room ADD CONSTRAINT FK_729F519B6C7FFB80 FOREIGN KEY (id_as_id) REFERENCES acquisition_system (id)');
        $this->addSql('DROP INDEX UNIQ_13C61622EA5C2A4A ON acquisition_system');
    }
}
