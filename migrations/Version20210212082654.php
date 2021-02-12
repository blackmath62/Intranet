<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20210212082654 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE FAQ (id INT AUTO_INCREMENT NOT NULL, user_id INT NOT NULL, logiciel_id INT NOT NULL, search_id INT NOT NULL, title VARCHAR(255) NOT NULL, content LONGTEXT NOT NULL, createdAt DATETIME NOT NULL, INDEX IDX_7E583746A76ED395 (user_id), INDEX IDX_7E583746CA84195D (logiciel_id), INDEX IDX_7E583746650760A9 (search_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE FAQ ADD CONSTRAINT FK_7E583746A76ED395 FOREIGN KEY (user_id) REFERENCES Users (id)');
        $this->addSql('ALTER TABLE FAQ ADD CONSTRAINT FK_7E583746CA84195D FOREIGN KEY (logiciel_id) REFERENCES Logiciel (id)');
        $this->addSql('ALTER TABLE FAQ ADD CONSTRAINT FK_7E583746650760A9 FOREIGN KEY (search_id) REFERENCES SectionSearch (id)');
        $this->addSql('ALTER TABLE logiciel CHANGE closedAt closedAt DATETIME NOT NULL');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE FAQ');
        $this->addSql('ALTER TABLE Logiciel CHANGE closedAt closedAt DATETIME DEFAULT NULL');
    }
}
