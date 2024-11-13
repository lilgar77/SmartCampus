<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241113101403 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE floor (id INT AUTO_INCREMENT NOT NULL, id_building_id INT NOT NULL, number_floor INT NOT NULL, INDEX IDX_BE45D62E5538B3E5 (id_building_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE floor ADD CONSTRAINT FK_BE45D62E5538B3E5 FOREIGN KEY (id_building_id) REFERENCES building (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE floor DROP FOREIGN KEY FK_BE45D62E5538B3E5');
        $this->addSql('DROP TABLE floor');
    }
}
