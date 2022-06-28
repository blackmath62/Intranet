<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220623100112 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ConduiteDeTravauxMe (id INT AUTO_INCREMENT NOT NULL, codeClient VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, adresseLivraison VARCHAR(255) NOT NULL, affaire VARCHAR(255) NOT NULL, modeDeTransport VARCHAR(255) NOT NULL, op VARCHAR(255) NOT NULL, dateCmd DATETIME NOT NULL, numCmd VARCHAR(255) NOT NULL, dateBl DATETIME DEFAULT NULL, numeroBl VARCHAR(255) DEFAULT NULL, dateFacture DATETIME DEFAULT NULL, numeroFacture VARCHAR(255) DEFAULT NULL, delaiDemande DATETIME DEFAULT NULL, delaiAccepte DATETIME DEFAULT NULL, delaiReporte DATETIME DEFAULT NULL, dateDebutChantier DATETIME DEFAULT NULL, dateFinChantier DATETIME DEFAULT NULL, etat VARCHAR(255) NOT NULL, dureeTravaux VARCHAR(255) DEFAULT NULL, updatedAt DATETIME NOT NULL, updatedBy_id INT DEFAULT NULL, INDEX IDX_9DCFA16D65FF1AEC (updatedBy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ConduiteDeTravauxMe ADD CONSTRAINT FK_9DCFA16D65FF1AEC FOREIGN KEY (updatedBy_id) REFERENCES Users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ConduiteDeTravauxMe');
    }
}
