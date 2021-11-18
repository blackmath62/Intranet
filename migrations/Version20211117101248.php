<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20211117101248 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE ListCmdTraite (id INT AUTO_INCREMENT NOT NULL, numero VARCHAR(255) NOT NULL, createdAt DATETIME NOT NULL, treatedBy_id INT DEFAULT NULL, UNIQUE INDEX UNIQ_70C87BA749F4847C (treatedBy_id), PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE ListCmdTraite ADD CONSTRAINT FK_70C87BA749F4847C FOREIGN KEY (treatedBy_id) REFERENCES Users (id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('DROP TABLE ListCmdTraite');
    }
}
