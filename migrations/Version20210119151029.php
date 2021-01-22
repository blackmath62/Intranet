<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210119151029 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE Annuaire (id INT AUTO_INCREMENT NOT NULL, societe_id INT NOT NULL, interne INT DEFAULT NULL, nom VARCHAR(255) NOT NULL, exterieur VARCHAR(255) DEFAULT NULL, mail VARCHAR(255) DEFAULT NULL, fonction VARCHAR(255) DEFAULT NULL, portable VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_BC1DC55D6C6E55B5 (nom), INDEX IDX_BC1DC55DFCF77503 (societe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Chats (id INT AUTO_INCREMENT NOT NULL, user_id INT DEFAULT NULL, content LONGTEXT NOT NULL, createdAt DATETIME NOT NULL, INDEX IDX_ECA9370BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Comments (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, ticket_id INT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, createdAt DATETIME NOT NULL, files VARCHAR(255) DEFAULT NULL, INDEX IDX_A6E8F47CA76ED395 (user_id), INDEX IDX_A6E8F47C700047D2 (ticket_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Documents (id INT AUTO_INCREMENT NOT NULL, societe_id INT DEFAULT NULL, user_id INT NOT NULL, createdAt DATETIME NOT NULL, title VARCHAR(255) NOT NULL, url VARCHAR(255) NOT NULL, beginningDate DATETIME NOT NULL, endDate DATETIME NOT NULL, INDEX IDX_2041F02BFCF77503 (societe_id), INDEX IDX_2041F02BA76ED395 (user_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Features (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL, active TINYINT(1) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Permissions (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, feature_id INT NOT NULL, INDEX IDX_AB7143B8A76ED395 (user_id), INDEX IDX_AB7143B860E4B879 (feature_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Prestataire (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) DEFAULT NULL, email VARCHAR(255) DEFAULT NULL, affiliation VARCHAR(255) DEFAULT NULL, phone VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Priorities (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, color VARCHAR(255) DEFAULT NULL, textColor VARCHAR(255) NOT NULL, closedAt DATETIME NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Services (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, color VARCHAR(255) DEFAULT NULL, createdAt DATETIME NOT NULL, closedAt DATETIME DEFAULT NULL, textColor VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Societe (id INT AUTO_INCREMENT NOT NULL, nom VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL, closedAt DATETIME DEFAULT NULL, dossier VARCHAR(255) NOT NULL, img VARCHAR(255) DEFAULT NULL, UNIQUE INDEX UNIQ_D6D804216C6E55B5 (nom), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Status (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL, ClosedAt DATETIME DEFAULT NULL, backgroundColor VARCHAR(255) NOT NULL, textColor VARCHAR(255) NOT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Tickets (id INT AUTO_INCREMENT NOT NULL, service_id INT NOT NULL, statu_id INT NOT NULL, societe_id INT DEFAULT NULL, user_id INT NOT NULL, priority_id INT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, createdAt DATETIME NOT NULL, closedAt DATETIME DEFAULT NULL, file VARCHAR(255) DEFAULT NULL, INDEX IDX_9BFBA468ED5CA9E6 (service_id), INDEX IDX_9BFBA46855C16B5E (statu_id), INDEX IDX_9BFBA468FCF77503 (societe_id), INDEX IDX_9BFBA468A76ED395 (user_id), INDEX IDX_9BFBA468497B19F9 (priority_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('CREATE TABLE Users (id INT AUTO_INCREMENT NOT NULL, societe_id INT NOT NULL, email VARCHAR(255) NOT NULL, password VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL, token VARCHAR(255) DEFAULT NULL, pseudo VARCHAR(255) DEFAULT NULL, img VARCHAR(255) DEFAULT NULL, roles JSON NOT NULL, bornAt DATETIME DEFAULT NULL, UNIQUE INDEX UNIQ_D5428AEDE7927C74 (email), UNIQUE INDEX UNIQ_D5428AED86CC499D (pseudo), INDEX IDX_D5428AEDFCF77503 (societe_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE Annuaire ADD CONSTRAINT FK_BC1DC55DFCF77503 FOREIGN KEY (societe_id) REFERENCES Societe (id)');
        $this->addSql('ALTER TABLE Chats ADD CONSTRAINT FK_ECA9370BA76ED395 FOREIGN KEY (user_id) REFERENCES Users (id)');
        $this->addSql('ALTER TABLE Comments ADD CONSTRAINT FK_A6E8F47CA76ED395 FOREIGN KEY (user_id) REFERENCES Users (id)');
        $this->addSql('ALTER TABLE Comments ADD CONSTRAINT FK_A6E8F47C700047D2 FOREIGN KEY (ticket_id) REFERENCES Tickets (id)');
        $this->addSql('ALTER TABLE Documents ADD CONSTRAINT FK_2041F02BFCF77503 FOREIGN KEY (societe_id) REFERENCES Societe (id)');
        $this->addSql('ALTER TABLE Documents ADD CONSTRAINT FK_2041F02BA76ED395 FOREIGN KEY (user_id) REFERENCES Users (id)');
        $this->addSql('ALTER TABLE Permissions ADD CONSTRAINT FK_AB7143B8A76ED395 FOREIGN KEY (user_id) REFERENCES Users (id)');
        $this->addSql('ALTER TABLE Permissions ADD CONSTRAINT FK_AB7143B860E4B879 FOREIGN KEY (feature_id) REFERENCES Features (id)');
        $this->addSql('ALTER TABLE Tickets ADD CONSTRAINT FK_9BFBA468ED5CA9E6 FOREIGN KEY (service_id) REFERENCES Services (id)');
        $this->addSql('ALTER TABLE Tickets ADD CONSTRAINT FK_9BFBA46855C16B5E FOREIGN KEY (statu_id) REFERENCES Status (id)');
        $this->addSql('ALTER TABLE Tickets ADD CONSTRAINT FK_9BFBA468FCF77503 FOREIGN KEY (societe_id) REFERENCES Societe (id)');
        $this->addSql('ALTER TABLE Tickets ADD CONSTRAINT FK_9BFBA468A76ED395 FOREIGN KEY (user_id) REFERENCES Users (id)');
        $this->addSql('ALTER TABLE Tickets ADD CONSTRAINT FK_9BFBA468497B19F9 FOREIGN KEY (priority_id) REFERENCES Priorities (id)');
        $this->addSql('ALTER TABLE Users ADD CONSTRAINT FK_D5428AEDFCF77503 FOREIGN KEY (societe_id) REFERENCES Societe (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Permissions DROP FOREIGN KEY FK_AB7143B860E4B879');
        $this->addSql('ALTER TABLE Tickets DROP FOREIGN KEY FK_9BFBA468497B19F9');
        $this->addSql('ALTER TABLE Tickets DROP FOREIGN KEY FK_9BFBA468ED5CA9E6');
        $this->addSql('ALTER TABLE Annuaire DROP FOREIGN KEY FK_BC1DC55DFCF77503');
        $this->addSql('ALTER TABLE Documents DROP FOREIGN KEY FK_2041F02BFCF77503');
        $this->addSql('ALTER TABLE Tickets DROP FOREIGN KEY FK_9BFBA468FCF77503');
        $this->addSql('ALTER TABLE Users DROP FOREIGN KEY FK_D5428AEDFCF77503');
        $this->addSql('ALTER TABLE Tickets DROP FOREIGN KEY FK_9BFBA46855C16B5E');
        $this->addSql('ALTER TABLE Comments DROP FOREIGN KEY FK_A6E8F47C700047D2');
        $this->addSql('ALTER TABLE Chats DROP FOREIGN KEY FK_ECA9370BA76ED395');
        $this->addSql('ALTER TABLE Comments DROP FOREIGN KEY FK_A6E8F47CA76ED395');
        $this->addSql('ALTER TABLE Documents DROP FOREIGN KEY FK_2041F02BA76ED395');
        $this->addSql('ALTER TABLE Permissions DROP FOREIGN KEY FK_AB7143B8A76ED395');
        $this->addSql('ALTER TABLE Tickets DROP FOREIGN KEY FK_9BFBA468A76ED395');
        $this->addSql('DROP TABLE Annuaire');
        $this->addSql('DROP TABLE Chats');
        $this->addSql('DROP TABLE Comments');
        $this->addSql('DROP TABLE Documents');
        $this->addSql('DROP TABLE Features');
        $this->addSql('DROP TABLE Permissions');
        $this->addSql('DROP TABLE Prestataire');
        $this->addSql('DROP TABLE Priorities');
        $this->addSql('DROP TABLE Services');
        $this->addSql('DROP TABLE Societe');
        $this->addSql('DROP TABLE Status');
        $this->addSql('DROP TABLE Tickets');
        $this->addSql('DROP TABLE Users');
    }
}
