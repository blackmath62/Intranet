<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20230427100056 extends AbstractMigration
{
    public function getDescription(): string
    {
        return '';
    }

    public function up(Schema $schema): void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Affaires (id INT AUTO_INCREMENT NOT NULL, code VARCHAR(255) NOT NULL, libelle VARCHAR(255) DEFAULT NULL, tiers VARCHAR(255) NOT NULL, nom VARCHAR(255) NOT NULL, progress INT DEFAULT NULL, start DATETIME DEFAULT NULL, end DATETIME DEFAULT NULL, textColor VARCHAR(255) DEFAULT NULL, backgroundColor VARCHAR(255) DEFAULT NULL, etat VARCHAR(255) NOT NULL, duration VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE affaires_equipe (affaires_id INT NOT NULL, equipe_id INT NOT NULL, INDEX IDX_E8F051FFFC809AB9 (affaires_id), INDEX IDX_E8F051FF6D861B89 (equipe_id), PRIMARY KEY(affaires_id, equipe_id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE affaires_equipe ADD CONSTRAINT FK_E8F051FFFC809AB9 FOREIGN KEY (affaires_id) REFERENCES Affaires (id) ON DELETE CASCADE');
        $this->addSql('ALTER TABLE affaires_equipe ADD CONSTRAINT FK_E8F051FF6D861B89 FOREIGN KEY (equipe_id) REFERENCES Equipe (id) ON DELETE CASCADE');
        $this->addSql('CREATE UNIQUE INDEX UNIQ_23E5BF236C6E55B5 ON equipe (nom)');
    }

    public function down(Schema $schema): void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE affaires_equipe DROP FOREIGN KEY FK_E8F051FFFC809AB9');
        $this->addSql('DROP TABLE Affaires');
        $this->addSql('DROP TABLE affaires_equipe');
        $this->addSql('DROP INDEX UNIQ_23E5BF236C6E55B5 ON Equipe');
    }
}
