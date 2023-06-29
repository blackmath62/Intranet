<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230629100322 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE SignatureElectronique (id INT AUTO_INCREMENT NOT NULL, intervention_id INT DEFAULT NULL, signatureId VARCHAR(255) DEFAULT NULL, documentId VARCHAR(255) DEFAULT NULL, signerId VARCHAR(255) DEFAULT NULL, pdfSansSignature VARCHAR(255) DEFAULT NULL, createdAt DATETIME NOT NULL, createdBy_id INT DEFAULT NULL, INDEX IDX_F0FE9688EAE3863 (intervention_id), INDEX IDX_F0FE9683174800F (createdBy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE SignatureElectronique ADD CONSTRAINT FK_F0FE9688EAE3863 FOREIGN KEY (intervention_id) REFERENCES InterventionMonteurs (id)');
        $this->addSql('ALTER TABLE SignatureElectronique ADD CONSTRAINT FK_F0FE9683174800F FOREIGN KEY (createdBy_id) REFERENCES Users (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE SignatureElectronique');
    }
}
