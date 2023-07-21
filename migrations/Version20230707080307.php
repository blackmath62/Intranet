<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230707080307 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE interventionfichemonteur ADD validedAt DATETIME DEFAULT NULL, ADD validedBy_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE interventionfichemonteur ADD CONSTRAINT FK_A8944028A9AD00D0 FOREIGN KEY (validedBy_id) REFERENCES Users (id)');
        $this->addSql('CREATE INDEX IDX_A8944028A9AD00D0 ON interventionfichemonteur (validedBy_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE InterventionFicheMonteur DROP FOREIGN KEY FK_A8944028A9AD00D0');
        $this->addSql('DROP INDEX IDX_A8944028A9AD00D0 ON InterventionFicheMonteur');
        $this->addSql('ALTER TABLE InterventionFicheMonteur DROP validedAt, DROP validedBy_id');
    }
}
