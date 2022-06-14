<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220426145736 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE MovBillFsc (id INT AUTO_INCREMENT NOT NULL, createdAt DATETIME NOT NULL, facture INT NOT NULL, dateFact DATETIME NOT NULL, tiers VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, notreRef VARCHAR(255) DEFAULT NULL, TypeTiers VARCHAR(255) NOT NULL, ventilation LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', createdBy_id INT DEFAULT NULL, INDEX IDX_14C831943174800F (createdBy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE MovBillFsc ADD CONSTRAINT FK_14C831943174800F FOREIGN KEY (createdBy_id) REFERENCES Users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE MovBillFsc');
    }
}
