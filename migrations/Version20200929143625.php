<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20200929143625 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('CREATE TABLE priorities (id INT AUTO_INCREMENT NOT NULL, title VARCHAR(255) NOT NULL, color VARCHAR(255) DEFAULT NULL, PRIMARY KEY(id)) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_unicode_ci` ENGINE = InnoDB');
        $this->addSql('ALTER TABLE comments ADD ticket_id INT NOT NULL');
        $this->addSql('ALTER TABLE comments ADD CONSTRAINT FK_5F9E962A700047D2 FOREIGN KEY (ticket_id) REFERENCES tickets (id)');
        $this->addSql('CREATE INDEX IDX_5F9E962A700047D2 ON comments (ticket_id)');
        $this->addSql('ALTER TABLE tickets ADD priority_id INT NOT NULL');
        $this->addSql('ALTER TABLE tickets ADD CONSTRAINT FK_54469DF4497B19F9 FOREIGN KEY (priority_id) REFERENCES priorities (id)');
        $this->addSql('CREATE INDEX IDX_54469DF4497B19F9 ON tickets (priority_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE tickets DROP FOREIGN KEY FK_54469DF4497B19F9');
        $this->addSql('DROP TABLE priorities');
        $this->addSql('ALTER TABLE comments DROP FOREIGN KEY FK_5F9E962A700047D2');
        $this->addSql('DROP INDEX IDX_5F9E962A700047D2 ON comments');
        $this->addSql('ALTER TABLE comments DROP ticket_id');
        $this->addSql('DROP INDEX IDX_54469DF4497B19F9 ON tickets');
        $this->addSql('ALTER TABLE tickets DROP priority_id');
    }
}
