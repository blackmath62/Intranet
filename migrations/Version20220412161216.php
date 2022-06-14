<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220412161216 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE PaysBanFsc (id INT AUTO_INCREMENT NOT NULL, pays VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL, CreatedBy_id INT DEFAULT NULL, INDEX IDX_E69CA5DB29455BF7 (CreatedBy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE PaysBanFsc ADD CONSTRAINT FK_E69CA5DB29455BF7 FOREIGN KEY (CreatedBy_id) REFERENCES Users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE PaysBanFsc');
    }
}
