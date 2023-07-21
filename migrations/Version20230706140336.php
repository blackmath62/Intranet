<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230706140336 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE InterventionFicheMonteur (id INT AUTO_INCREMENT NOT NULL, intervenant_id INT DEFAULT NULL, commentaire_id INT DEFAULT NULL, createdAt DATETIME NOT NULL, pension LONGTEXT DEFAULT NULL COMMENT \'(DC2Type:array)\', createdBy_id INT DEFAULT NULL, INDEX IDX_A89440283174800F (createdBy_id), INDEX IDX_A8944028AB9A1716 (intervenant_id), UNIQUE INDEX UNIQ_A8944028BA9CD190 (commentaire_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE InterventionFichesMonteursHeures (id INT AUTO_INCREMENT NOT NULL, type VARCHAR(255) NOT NULL, moment VARCHAR(255) NOT NULL, start TIME NOT NULL, end TIME NOT NULL, createdAt DATETIME NOT NULL, createdBy_id INT DEFAULT NULL, interventionFicheMonteur_id INT DEFAULT NULL, INDEX IDX_8CF402813174800F (createdBy_id), INDEX IDX_8CF40281EBED9C32 (interventionFicheMonteur_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE InterventionFicheMonteur ADD CONSTRAINT FK_A89440283174800F FOREIGN KEY (createdBy_id) REFERENCES Users (id)');
        $this->addSql('ALTER TABLE InterventionFicheMonteur ADD CONSTRAINT FK_A8944028AB9A1716 FOREIGN KEY (intervenant_id) REFERENCES Users (id)');
        $this->addSql('ALTER TABLE InterventionFicheMonteur ADD CONSTRAINT FK_A8944028BA9CD190 FOREIGN KEY (commentaire_id) REFERENCES Commentaires (id)');
        $this->addSql('ALTER TABLE InterventionFichesMonteursHeures ADD CONSTRAINT FK_8CF402813174800F FOREIGN KEY (createdBy_id) REFERENCES Users (id)');
        $this->addSql('ALTER TABLE InterventionFichesMonteursHeures ADD CONSTRAINT FK_8CF40281EBED9C32 FOREIGN KEY (interventionFicheMonteur_id) REFERENCES InterventionFicheMonteur (id)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE InterventionFichesMonteursHeures DROP FOREIGN KEY FK_8CF40281EBED9C32');
        $this->addSql('DROP TABLE InterventionFicheMonteur');
        $this->addSql('DROP TABLE InterventionFichesMonteursHeures');
    }
}
