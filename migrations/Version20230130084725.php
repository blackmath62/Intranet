<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230130084725 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE RetraitMarchandisesEan (id INT AUTO_INCREMENT NOT NULL, createdAt DATETIME NOT NULL, ean INT NOT NULL, quantite INT NOT NULL, chantier VARCHAR(255) NOT NULL, sendAt DATETIME DEFAULT NULL, createdBy_id INT DEFAULT NULL, INDEX IDX_3A0E9F693174800F (createdBy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE RetraitMarchandisesEan ADD CONSTRAINT FK_3A0E9F693174800F FOREIGN KEY (createdBy_id) REFERENCES Users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE RetraitMarchandisesEan');
    }
}
