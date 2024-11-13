<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20241113115456 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE floor DROP FOREIGN KEY FK_BE45D62E5538B3E5');
        $this->addSql('ALTER TABLE floor ADD CONSTRAINT FK_BE45D62E5538B3E5 FOREIGN KEY (id_building_id) REFERENCES building (id) ON DELETE CASCADE');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE floor DROP FOREIGN KEY FK_BE45D62E5538B3E5');
        $this->addSql('ALTER TABLE floor ADD CONSTRAINT FK_BE45D62E5538B3E5 FOREIGN KEY (id_building_id) REFERENCES building (id)');
    }
}
