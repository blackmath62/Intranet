<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230616133132 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE affairepiece ADD interventionMonteurs_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE affairepiece ADD CONSTRAINT FK_50D0B71A2A43AA1F FOREIGN KEY (interventionMonteurs_id) REFERENCES InterventionMonteurs (id)');
        $this->addSql('CREATE INDEX IDX_50D0B71A2A43AA1F ON affairepiece (interventionMonteurs_id)');
        $this->addSql('ALTER TABLE interventionmonteurs ADD backgroundColor VARCHAR(255) DEFAULT NULL, ADD textColor VARCHAR(255) DEFAULT NULL');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE AffairePiece DROP FOREIGN KEY FK_50D0B71A2A43AA1F');
        $this->addSql('DROP INDEX IDX_50D0B71A2A43AA1F ON AffairePiece');
        $this->addSql('ALTER TABLE AffairePiece DROP interventionMonteurs_id');
        $this->addSql('ALTER TABLE InterventionMonteurs DROP backgroundColor, DROP textColor');
    }
}
