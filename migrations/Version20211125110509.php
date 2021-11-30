<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211125110509 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE CmdRobyDelaiAccepteReporte (id INT AUTO_INCREMENT NOT NULL, identification VARCHAR(255) NOT NULL, statut VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL, modifiedAt DATETIME DEFAULT NULL, tiers VARCHAR(255) NOT NULL, Nom VARCHAR(255) NOT NULL, dateCmd DATETIME NOT NULL, notreRef VARCHAR(255) DEFAULT NULL, delaiAccepte DATETIME DEFAULT NULL, delaiReporte DATETIME DEFAULT NULL, note LONGTEXT DEFAULT NULL, modifiedBy_id INT DEFAULT NULL, INDEX IDX_F1BB5812D6A05076 (modifiedBy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE CmdRobyDelaiAccepteReporte ADD CONSTRAINT FK_F1BB5812D6A05076 FOREIGN KEY (modifiedBy_id) REFERENCES Users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE CmdRobyDelaiAccepteReporte');
    }
}
