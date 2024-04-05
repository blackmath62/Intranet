<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20240328152732 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE interventionmonteurs ADD allDay TINYINT(1) NOT NULL');
        $this->addSql('ALTER TABLE mouvpreparationcmdadmin RENAME INDEX idx_4dba2fcdf7b85e2 TO IDX_9CA10AE3DF7B85E2');
        $this->addSql('ALTER TABLE mouvpreparationcmdadmin RENAME INDEX idx_4dba2fce8b94f96 TO IDX_9CA10AE3E8B94F96');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE InterventionMonteurs DROP allDay');
        $this->addSql('ALTER TABLE MouvPreparationCmdAdmin RENAME INDEX idx_9ca10ae3df7b85e2 TO IDX_4DBA2FCDF7B85E2');
        $this->addSql('ALTER TABLE MouvPreparationCmdAdmin RENAME INDEX idx_9ca10ae3e8b94f96 TO IDX_4DBA2FCE8B94F96');
    }
}
