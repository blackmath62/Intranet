<?php

declare(strict_types=1);

namespace DoctrineMigrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

/**
 * Auto-generated Migration: Please modify to your needs!
 */
final class Version20220719100310 extends AbstractMigration
{
    public function getDescription() : string
    {
        return '';
    }

    public function up(Schema $schema) : void
    {
        // this up() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE holiday ADD user_id INT DEFAULT NULL');
        $this->addSql('ALTER TABLE holiday ADD CONSTRAINT FK_13278BA8A76ED395 FOREIGN KEY (user_id) REFERENCES Users (id)');
        $this->addSql('CREATE INDEX IDX_13278BA8A76ED395 ON holiday (user_id)');
    }

    public function down(Schema $schema) : void
    {
        // this down() migration is auto-generated, please modify it to your needs
        $this->addSql('ALTER TABLE Holiday DROP FOREIGN KEY FK_13278BA8A76ED395');
        $this->addSql('DROP INDEX IDX_13278BA8A76ED395 ON Holiday');
        $this->addSql('ALTER TABLE Holiday DROP user_id');
    }
}
