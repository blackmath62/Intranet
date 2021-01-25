<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210123175815 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tickets ADD prestataire_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_9BFBA468BE3DB2B7 FOREIGN KEY (prestataire_id) REFERENCES Prestataire (id)');
        $this->addSql('CREATE INDEX IDX_9BFBA468BE3DB2B7 ON tickets (prestataire_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Tickets DROP FOREIGN KEY FK_9BFBA468BE3DB2B7');
        $this->addSql('DROP INDEX IDX_9BFBA468BE3DB2B7 ON Tickets');
        $this->addSql('ALTER TABLE Tickets DROP prestataire_id');
    }
}
