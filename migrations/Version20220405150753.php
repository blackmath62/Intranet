<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220405150753 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ControleArticlesFsc (id INT AUTO_INCREMENT NOT NULL, createdAt DATETIME NOT NULL, UpdatedAt DATETIME NOT NULL, products VARCHAR(255) NOT NULL, status TINYINT(1) NOT NULL, controledBy_id INT DEFAULT NULL, INDEX IDX_824088FF96D13A0E (controledBy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ControleArticlesFsc ADD CONSTRAINT FK_824088FF96D13A0E FOREIGN KEY (controledBy_id) REFERENCES Users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ControleArticlesFsc');
    }
}
