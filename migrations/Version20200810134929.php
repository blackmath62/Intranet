<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200810134929 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annuaire ADD societe_id INT NOT NULL');
        $this->addSql('ALTER TABLE annuaire ADD CONSTRAINT FK_456BA70BFCF77503 FOREIGN KEY (societe_id) REFERENCES societe (id)');
        $this->addSql('CREATE INDEX IDX_456BA70BFCF77503 ON annuaire (societe_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE annuaire DROP FOREIGN KEY FK_456BA70BFCF77503');
        $this->addSql('DROP INDEX IDX_456BA70BFCF77503 ON annuaire');
        $this->addSql('ALTER TABLE annuaire DROP societe_id');
    }
}
