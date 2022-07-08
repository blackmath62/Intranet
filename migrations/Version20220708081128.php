<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220708081128 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE documentsreglementairesfsc ADD addBy_id INT DEFAULT NULL, DROP addBy');
        $this->addSql('ALTER TABLE documentsreglementairesfsc ADD CONSTRAINT FK_E3BDB82738FBA2EB FOREIGN KEY (addBy_id) REFERENCES Users (id)');
        $this->addSql('CREATE INDEX IDX_E3BDB82738FBA2EB ON documentsreglementairesfsc (addBy_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE DocumentsReglementairesFsc DROP FOREIGN KEY FK_E3BDB82738FBA2EB');
        $this->addSql('DROP INDEX IDX_E3BDB82738FBA2EB ON DocumentsReglementairesFsc');
        $this->addSql('ALTER TABLE DocumentsReglementairesFsc ADD addBy VARCHAR(255) CHARACTER SET utf8mb4 NOT NULL COLLATE `utf8mb4_unicode_ci`, DROP addBy_id');
    }
}
