<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20231106160123 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE interventionmonteurs ADD typeIntervention_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE interventionmonteurs ADD CONSTRAINT FK_3B754F178CD172E6 FOREIGN KEY (typeIntervention_id) REFERENCES StatutsGeneraux (id)');
        $this->addSql('CREATE INDEX IDX_3B754F178CD172E6 ON interventionmonteurs (typeIntervention_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE InterventionMonteurs DROP FOREIGN KEY FK_3B754F178CD172E6');
        $this->addSql('DROP INDEX IDX_3B754F178CD172E6 ON InterventionMonteurs');
        $this->addSql('ALTER TABLE InterventionMonteurs DROP typeIntervention_id');
    }
}
