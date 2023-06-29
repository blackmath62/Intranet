<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230622095127 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE interventionmonteurs ADD code_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE interventionmonteurs ADD CONSTRAINT FK_3B754F1727DAFE17 FOREIGN KEY (code_id) REFERENCES Affaires (id)');
        $this->addSql('CREATE INDEX IDX_3B754F1727DAFE17 ON interventionmonteurs (code_id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE InterventionMonteurs DROP FOREIGN KEY FK_3B754F1727DAFE17');
        $this->addSql('DROP INDEX IDX_3B754F1727DAFE17 ON InterventionMonteurs');
        $this->addSql('ALTER TABLE InterventionMonteurs DROP code_id');
    }
}
